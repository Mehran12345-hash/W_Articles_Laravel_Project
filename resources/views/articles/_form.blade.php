@php($editing = $article !== null)
<form class="editor-grid" method="POST" action="{{ $editing ? route('articles.update', $article) : route('articles.store') }}" enctype="multipart/form-data">
    @csrf
    @if($editing) @method('PUT') @endif
    <section class="surface-card editor-main"><div class="section-heading"><h2>Article details</h2><p>Give your story a title and bring it to life.</p></div>
        <label class="field-label" for="title">Article title <span class="required">*</span></label>
        <input class="field" id="title" name="title" value="{{ old('title', $article?->title) }}" maxlength="180" required placeholder="e.g. The art of telling great stories">
        <label class="field-label" for="excerpt">Short description</label>
        <textarea class="field textarea-short" id="excerpt" name="excerpt" rows="3" maxlength="300" placeholder="Write a short summary of your article...">{{ old('excerpt', $article?->excerpt) }}</textarea>
        <label class="field-label" for="body">Article content <span class="required">*</span></label>
        <textarea class="field textarea-large" id="body" name="body" rows="15" minlength="10" required placeholder="Start writing your story here...">{{ old('body', $article?->body) }}</textarea>
        <p class="input-hint">Tip: Start with a clear opening. You can use paragraphs and line breaks.</p>
    </section>
    <div class="editor-aside"><section class="surface-card editor-side-card"><div class="section-heading"><h2>Publishing</h2><p>Choose how your article appears.</p></div>
        <label class="field-label" for="status">Status</label>
        <select class="field" id="status" name="status" required><option value="draft" @selected(old('status', $article?->status ?? 'draft') === 'draft')>Draft</option><option value="published" @selected(old('status', $article?->status) === 'published')>Published</option></select>
        <label class="field-label" for="category">Category <span class="required">*</span></label>
        <input class="field" id="category" name="category" value="{{ old('category', $article?->category) }}" maxlength="60" required placeholder="e.g. Design, Technology">
        <button type="submit" class="btn btn-primary btn-block">{{ $editing ? 'Save changes' : 'Save article' }} <span>→</span></button>
        <a class="btn btn-subtle btn-block" href="{{ route('articles.index') }}">Cancel</a>
    </section>
    <section class="surface-card editor-side-card"><div class="section-heading"><h2>Cover image</h2><p>Make your article stand out.</p></div>
        @if($editing && $article->cover_image)<img class="current-cover" src="{{ asset('storage/'.$article->cover_image) }}" alt="Current cover image"><label class="remember"><input type="checkbox" name="remove_cover" value="1" @checked(old('remove_cover'))> Remove current cover</label>@endif
        <input type="file" class="field upload-field" id="cover_image" name="cover_image" accept="image/jpeg,image/png,image/webp">
        <p class="input-hint">JPG, PNG or WebP. Maximum 3 MB.</p>
    </section></div>
</form>
