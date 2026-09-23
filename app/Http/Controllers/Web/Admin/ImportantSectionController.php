<?php

namespace App\Http\Controllers\Web\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ImportantSectionStoreRequest;
use App\Http\Requests\Admin\ImportantSectionUpdateRequest;
use App\Http\Requests\Admin\SectionOrderUpdateRequest;
use App\Models\ImportantSection;
use App\Models\ImportantLink;

class ImportantSectionController extends Controller
{
    public function index()
    {
        $sections = ImportantSection::filter(request(['search']))
            ->orderBy('name', 'asc')->with('importantLinks')->paginate(10)->withQueryString();

        return view('admin.sections.index', [
            'sections' => $sections
        ]);
    }

    public function reorder()
    {
        $sections = ImportantSection::orderBy('order_number')->get();

        return view('admin.sections.reorder', [
            'sections' => $sections
        ]);
    }

    public function updateReorder(SectionOrderUpdateRequest $request)
    {
        $orders = $request->validated()['order'];

        foreach ($orders as $sectionId => $order) {
            ImportantSection::where('id', $sectionId)->update([
                'order_number' => $order
            ]);
        }

        $request->session()->flash('success', 'Urutan berhasil diperbarui!');
        return redirect()->route('admin.sections.index');
    }


    public function create()
    {
        return view('admin.sections.create');
    }

    public function store(ImportantSectionStoreRequest $request)
    {
        $validated = $request->validated();

        if (count(ImportantSection::where('name', $validated['name'])->get()) != 0){
            return back()->withError('Section sudah ada');
        }

        $section = ImportantSection::create([
            'name' => $validated['name'],
            'order_number' => ImportantSection::count()+1
        ]);

        $data = ImportantSection::where('id','=',$section->id)->get();
        if ($data) {
            $request->session()->flash('success', 'Section berhasil ditambahkan!');
            return redirect()->route('admin.sections.index');
        } else {
            return back()->withErrors([
                'message' => 'Terdapat kesalahan'
            ]);
        }
    }

    public function edit(ImportantSection $importantSection)
    {
        $section = $importantSection;

        return view('admin.sections.edit', [
            'section' => $section
        ]);

    }

    public function update(ImportantSectionUpdateRequest $request, ImportantSection $importantSection)
    {
        $validated = $request->validated();

        $section = $importantSection;

        if ($section->name != $validated['name'] && count(ImportantSection::where('name', $validated['name'])->get()) != 0){
            return back()->withError('Section sudah ada');
        }

        $section->name = $validated['name'];
        $section->save();

        $data = ImportantSection::where('id','=',$section->id)->get();
        if ($data) {
            $request->session()->flash('success', 'Section berhasil diperbarui!');
            return redirect()->route('admin.sections.index');
        } else {
            return back()->withErrors([
                'message' => 'Terdapat kesalahan'
            ]);
        }
    }

    public function destroy(ImportantSection $importantSection)
    {
        $section = $importantSection;

        $links = ImportantLink::where('important_section_id', $section->id)->delete();

        $section->delete();

        request()->session()->flash('success', 'Section berhasil dihapus!');
        return redirect()->route('admin.sections.index');
    }
}
