@if (isset($selectedTags))
    @foreach ($selectedTags as $selectedTag)
        <label class="dropdown-item">
            <input name="tags[]" type="checkbox" class="checkbox-option" value="{{ $selectedTag->id }}"
                onclick="saveSelection(this)"> {{ $selectedTag->name }}
        </label>
    @endforeach
@endif
@foreach ($tags as $tag)
    <label class="dropdown-item">
        <input name="tags[]" type="checkbox" class="checkbox-option" value="{{ $tag->id }}"
            onclick="saveSelection(this)"> {{ $tag->name }}
    </label>
@endforeach
