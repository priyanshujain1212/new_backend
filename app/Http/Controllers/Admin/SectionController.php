<?php

namespace App\Http\Controllers\Admin;

use App\Enums\Status;
use App\Http\Controllers\BackendController;
use App\Http\Requests\SectionRequest;
use App\Models\Section;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Yajra\Datatables\Datatables;

class SectionController extends BackendController
{
    /**
     * ShopController constructor.
     */
    public function __construct()
    {
        parent::__construct();
        $this->data['sitetitle'] = 'Sections';
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('admin.section.index', $this->data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $this->data['sections'] = Section::where(['status' => Status::ACTIVE])->get();
        return view('admin.section.create', $this->data);
    }

    /**
     * @param SectionRequest $request
     * @return mixed
     */
    public function store(SectionRequest $request)
    {
        $section                = new Section;
        $section->name          = $request->name;
        $section->status        = $request->status;
        $section->save();
        error_log($section);
        //Store Image
        if ($request->hasFile('image') && $request->file('image')->isValid()) {
            $section->addMediaFromRequest('image')->toMediaCollection('sections');
        }

        return redirect(route('admin.section.index'))->withSuccess('The Data Inserted Successfully');
    }

        /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $this->data['section'] = Section::findOrFail($id);
        return view('admin.section.edit', $this->data);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(SectionRequest $request, $id)
    {
        $section                = Section::findOrFail($id);
        $section->name          = $request->name;
        $section->status        = $request->status;
        $section->save();

        //Store Image
        if ($request->hasFile('image') && $request->file('image')->isValid()) {
            $section->media()->delete($section->id);
            $section->addMediaFromRequest('image')->toMediaCollection('sections');
        }

        return redirect(route('admin.section.index'))->withSuccess('The Data Inserted Successfully');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        Section::findOrFail($id)->delete();
        return redirect(route('admin.section.index'))->withSuccess('The Data Deleted Successfully');
    }

    public function getSection(Request $request)
    {
        if (request()->ajax()) {
            if (!empty($request->status) && (int) $request->status) {
                $sections = Section::where(['status' => $request->status])->orderBy('id', 'desc')->get();
            } else {
                $sections = Section::orderBy('id', 'desc')->get();
            }

            $i             = 1;
            $sectionsArray = [];
            if (!blank($sections)) {
                foreach ($sections as $section) {
                    $sectionsArray[$i]          = $section;
                    $sectionsArray[$i]['setID'] = $i;
                    $i++;
                }
            }
            return Datatables::of($sectionsArray)
                ->addColumn('image', function ($section) {
                    if ($section->getFirstMediaUrl('sections')) {
                        return '<img alt="image" src="' . asset($section->getFirstMediaUrl('sections')) . '" class="rounded-circle mr-1 avatar-item">';
                    } else {
                        return '<img alt="image" src="' . asset('assets/img/default/section.png') . '" class="rounded-circle mr-1 avatar-item">';
                    }
                })
                ->addColumn('action', function ($section) {
                    return '<a href="' . route('admin.section.edit', $section) . '" class="btn btn-sm btn-icon float-left btn-primary"><i class="far fa-edit"></i></a><form class="float-left pl-2" action="' . route('admin.section.destroy', $section) . '" method="POST">' . method_field('DELETE') . csrf_field() . '<button class="btn btn-sm btn-icon btn-danger"><i class="fa fa-trash"></i></button></form>';
                })
                ->editColumn('status', function ($section) {
                    return ($section->status == 5 ? trans('statuses.' . Status::ACTIVE) : trans('statuses.' . Status::INACTIVE));
                })
                ->editColumn('id', function ($section) {
                    return $section->setID;
                })
                ->rawColumns(['image', 'action', 'description'])
                ->make(true);
        }
    }
}
