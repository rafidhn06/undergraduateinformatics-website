@extends('layouts.admin')

@section('title', 'Ubah ' . $post->title)

@section('content')
    <div class="admin modern-page">
        <h2 class="modern-page__heading">Form Ubah Informasi</h2>
        <div class="form row form--wide">
            @include('partials.alerts')
            <form method="POST" action="{{ route('admin.posts.update', ['post' => $post->id]) }}" enctype="multipart/form-data">
                @method('PUT')
                @csrf
                <div class="mb-3">
                    <label for="judul" class="form-label">
                        <h4>Judul<span class="required-star">*</span></h4>
                    </label>
                    <input name="title" type="text" class="form-control" id="judul" value="{{ $post->title }}"
                        required>
                </div>
                <div class="mb-3">
                    <label for="subjudul" class="form-label">
                        <h4>Sub-Judul<span class="required-star">*</span></h4>
                    </label>
                    <input name="subtitle" type="text" class="form-control" id="subjudul"
                        value="{{ $post->subtitle }}" required>
                </div>
                <div class="mb-3">
                    <label for="deskripsi" class="form-label">
                        <h4>Deskripsi<span class="required-star">*</span></h4>
                    </label>
                    <textarea name="body" type="text" class="form-control" id="deskripsi" required>{{ $post->body }}</textarea>
                </div>
                <script>
                    ClassicEditor
                        .create(document.querySelector('#deskripsi'), {
                            language: 'id',
                            toolbar: ['undo', 'redo', '|', 'heading', '|', 'bold', 'italic', '|', 'link', 'insertTable', 'blockQuote', '|', 'bulletedList', 'numberedList'],
                            heading: {
                                options: [
                                    { model: 'paragraph', title: 'Paragraph', class: 'ck-heading_paragraph' },
                                    { model: 'heading2', view: 'h2', title: 'Heading 2', class: 'ck-heading_heading2' },
                                    { model: 'heading3', view: 'h3', title: 'Heading 3', class: 'ck-heading_heading3' },
                                    { model: 'heading4', view: 'h4', title: 'Heading 4', class: 'ck-heading_heading4' }
                                ]
                            },
                            removePlugins: ['EasyImage', 'ImageUpload', 'MediaEmbed']
                        })
                        .catch(error => {
                            console.error(error);
                        });
                </script>
                <div class="row g-3">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="gambar" class="form-label">
                                <h4>Gambar</h4>
                            </label>
                            @if ($post->hasImage())
                                <div id="image-container" class="mb-2">
                                    <img class="form-image-preview" src="{{ asset('storage/' . $post->image) }}" alt="{{ $post->title }}">
                                    <label class="form-check-label">
                                        <input name="deleteGambar" type="checkbox" id="deleteGambar" onclick="deleteImage()"> Hapus Gambar
                                    </label>
                                </div>
                            @endif
                            <input name="image" type="file" accept="image/*" class="form-control" id="gambar">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="dropdownMenuButton" class="form-label">
                                <h4>Topik<span class="required-star">*</span></h4>
                            </label>
                            <div class="dropdown" onclick="performSearch()">
                                <button class="modern-button modern-button--neutral dropdown-toggle" type="button" id="dropdownMenuButton"
                                    data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    Topik yang dipilih bisa lebih dari 1
                                </button>
                                <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                    <input type="text" id="searchInput" name="searchInput" placeholder="Cari disini"
                                        autocomplete="off" oninput="performSearch()">
                                    <div id="searchResults">
                                        @foreach ($tags as $tag)
                                            <label class="dropdown-item">
                                                <input name="tags[]" id="{{ $tag->id }}" type="checkbox"
                                                    class="checkbox-option" onclick="saveSelection(this)"
                                                    value="{{ $tag->id }}"
                                                    {{ in_array($tag->id, $post->tags->pluck('id')->toArray()) ? 'checked' : '' }}>
                                                {{ $tag->name }}
                                            </label>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="mt-4 d-flex gap-2">
                    <button type="submit" class="modern-button modern-button--primary">Simpan</button>
                    <a href="{{ route('admin.posts.index') }}" class="modern-button modern-button--soft">Batal</a>
                </div>
            </form>
        </div>
    </div>
    @include('admin.posts.tag-live-search-script')
@endsection

<script>
    function deleteImage() {
        var deleteGambar = document.getElementById('deleteGambar');
        var imageContainer = document.getElementById('image-container');
        if (deleteGambar && imageContainer) {
            imageContainer.style.display = deleteGambar.checked ? 'none' : 'block';
        }
    }
</script>