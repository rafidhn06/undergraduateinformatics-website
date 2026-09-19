<?php

namespace App\Http\Controllers\Web\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ImportantLinkStoreRequest;
use App\Http\Requests\Admin\ImportantLinkUpdateRequest;
use App\Models\ImportantLink;
use App\Models\ImportantSection;

class ImportantLinkController extends Controller
{
    public function index()
    {
        $links = ImportantLink::filter(request(['search']))
            ->join('important_sections', 'important_sections.id', '=', 'important_links.important_section_id')
            ->orderBy('important_sections.name')
            ->orderBy('important_links.name', 'desc')
            ->select('important_links.*')
            ->with('important_section')
            ->paginate(10)
            ->withQueryString();

        return view("AdminLinkPenting.AdminPageLink", [
            'links' => $links
        ]);
    }

    public function create()
    {
        $sections = ImportantSection::query()->orderBy('name')->get();

        if ($sections->isEmpty()) {
            return redirect()->route('admin.sections.create')
                ->withError('Silahkan buat section link terlebih dahulu');
        }

        return view("AdminLinkPenting.AdminPageTambahLink", [
            'sections' => $sections
        ]);
    }

    public function store(ImportantLinkStoreRequest $request)
    {
        $validated = $request->validated();

        $link = ImportantLink::create([
            'important_section_id' => $validated['section_id'],
            'name' => $validated['name'],
            'link' => $validated['link']
        ]);

        $data = ImportantLink::where('id','=',$link->id)->get();
        if ($data) {
            $request->session()->flash('success', 'Link berhasil ditambahkan!');
            return redirect()->route('admin.links.index');
        } else {
            return back()->withError('Terdapat kesalahan');
        }
    }

    public function edit(ImportantLink $importantLink)
    {
        $importantLink->load('important_section');
        $sections = ImportantSection::query()->orderBy('name')->get();
        $link = $importantLink;

        return view("AdminLinkPenting.AdminPageEditLink", [
            'sections' => $sections,
            'link' => $link
        ]);
    }

    public function update(ImportantLinkUpdateRequest $request, ImportantLink $importantLink)
    {
        $validated = $request->validated();

        $link = $importantLink;

        $link->important_section_id = $validated['section_id'];
        $link->name = $validated['name'];
        $link->link = $validated['link'];
        $link->save();

        $data = ImportantLink::where('id','=',$link->id)->get();
        if ($data) {
            $request->session()->flash('success', 'Link berhasil diupdate!');
            return redirect()->route('admin.links.index');
        } else {
            return back()->withError('Terdapat kesalahan');
        }
    }

    public function destroy(ImportantLink $importantLink)
    {
        $link = $importantLink;

        $link->delete();

        request()->session()->flash('success', 'Link berhasil dihapus!');
        return redirect()->route('admin.links.index');
    }
}
