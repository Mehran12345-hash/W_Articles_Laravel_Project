<?php

namespace Tests\Feature;

use App\Models\Article;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ArticleManagementTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        app(\Spatie\Permission\PermissionRegistrar::class)->forgetCachedPermissions();
    }

    private function makeUser(string $role): User
    {
        $user = User::factory()->create();
        $user->assignRole($role);
        return $user;
    }

    private function articleFor(User $user, string $status = 'draft'): Article
    {
        return Article::create([
            'user_id' => $user->id,
            'title' => 'Sample Article',
            'category' => 'Learning',
            'body' => 'A complete sample article for Laravel testing.',
            'status' => $status,
        ]);
    }

    public function test_guest_can_read_published_blog_but_not_drafts(): void
    {
        $writer = $this->makeUser('editor');
        $published = $this->articleFor($writer, 'published');
        $draft = $this->articleFor($writer, 'draft');

        $this->get('/')->assertOk()->assertSee('Sample Article');
        $this->get('/blog/'.$published->id)->assertOk();
        $this->get('/blog/'.$draft->id)->assertNotFound();
        $this->get('/dashboard')->assertRedirect('/login');
    }

    public function test_new_registration_assigns_reader_role(): void
    {
        $this->post('/register', [
            'name' => 'New Reader',
            'email' => 'reader@example.test',
            'password' => 'test-password-123',
            'password_confirmation' => 'test-password-123',
        ])->assertRedirect('/dashboard');

        $this->assertAuthenticated();
        $this->assertTrue(User::where('email', 'reader@example.test')->firstOrFail()->hasRole('user'));
    }

    public function test_reader_cannot_write_or_delete_articles_or_open_team_access_pages(): void
    {
        $reader = $this->makeUser('user');
        $editor = $this->makeUser('editor');
        $article = $this->articleFor($editor, 'published');
        $this->actingAs($reader);

        $this->get('/articles')->assertOk();
        $this->get('/articles/create')->assertForbidden();
        $this->post('/articles', [
            'title' => 'Not Allowed', 'category' => 'Learning',
            'body' => 'This must not be stored by a reader.', 'status' => 'published',
        ])->assertForbidden();
        $this->delete('/articles/'.$article->id)->assertForbidden();
        $this->get('/users')->assertForbidden();
        $this->get('/roles')->assertForbidden();
        $this->get('/permissions')->assertForbidden();
    }

    public function test_editor_can_create_and_edit_own_article_but_not_delete_or_edit_others(): void
    {
        $editor = $this->makeUser('editor');
        $otherEditor = $this->makeUser('editor');
        $this->actingAs($editor);

        $this->post('/articles', [
            'title' => 'My First Article', 'category' => 'Writing',
            'excerpt' => 'My first post', 'body' => 'This is my first article with useful words.',
            'status' => 'published',
        ])->assertRedirect('/articles');

        $article = Article::where('user_id', $editor->id)->firstOrFail();
        $this->get('/articles/'.$article->id)->assertOk();
        $this->put('/articles/'.$article->id, [
            'title' => 'Updated Article', 'category' => 'Writing',
            'body' => 'Now this article has more updated information.',
            'status' => 'published',
        ])->assertRedirect('/articles');
        $this->assertDatabaseHas('articles', ['id' => $article->id, 'status' => 'published']);
        $this->delete('/articles/'.$article->id)->assertForbidden();

        $someoneElses = $this->articleFor($otherEditor, 'draft');
        $this->put('/articles/'.$someoneElses->id, [
            'title' => 'Wrong', 'category' => 'Writing',
            'body' => 'Should not be permitted by another author.', 'status' => 'published',
        ])->assertForbidden();
    }

    public function test_admin_can_manage_articles_and_assign_lower_roles_but_not_change_access_rules(): void
    {
        $admin = $this->makeUser('admin');
        $editor = $this->makeUser('editor');
        $article = $this->articleFor($editor, 'published');
        $this->actingAs($admin);

        $this->get('/users')->assertOk();
        $this->get('/roles')->assertForbidden();
        $this->get('/permissions')->assertForbidden();
        $this->patch('/users/'.$editor->id.'/role', ['role' => 'user'])->assertRedirect();
        $this->assertTrue($editor->fresh()->hasRole('user'));
        $this->patch('/users/'.$editor->id.'/role', ['role' => 'supervisor'])->assertSessionHasErrors('role');
        $this->delete('/articles/'.$article->id)->assertRedirect('/articles');
        $this->assertDatabaseMissing('articles', ['id' => $article->id]);
    }

    public function test_super_admin_can_open_separate_roles_and_permissions_pages(): void
    {
        $superAdmin = $this->makeUser('super-admin');
        $this->actingAs($superAdmin);

        $this->get('/roles')->assertOk()->assertSee('Role directory');
        $this->get('/permissions')->assertOk()->assertSee('Permission checklist');
        $this->get('/users')->assertOk();
    }

    public function test_supervisor_can_review_content_and_view_team_but_cannot_assign_roles(): void
    {
        $supervisor = $this->makeUser('supervisor');
        $editor = $this->makeUser('editor');
        $this->actingAs($supervisor);

        $this->get('/dashboard')->assertOk();
        $this->get('/users')->assertOk();
        $this->patch('/users/'.$editor->id.'/role', ['role' => 'user'])->assertForbidden();
        $this->get('/roles')->assertForbidden();
        $this->get('/permissions')->assertForbidden();
    }

    public function test_admin_cannot_change_their_own_role(): void
    {
        $admin = $this->makeUser('admin');
        $this->actingAs($admin);
        $this->patch('/users/'.$admin->id.'/role', ['role' => 'user'])->assertSessionHasErrors('role');
        $this->assertTrue($admin->fresh()->hasRole('admin'));
    }
}
