<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DevelopController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $extension = $file->getClientOriginalExtension();
            $version = str_replace('.', '_', $request->version); //將版本的點換成底線
            $file_name = date('ymdHisu') . $version . '.' . $extension;
            // return ($file_name);
            if ($file->isValid()) {
                $this->validate($request, [
                    'appName' => 'required|string|max:50',
                    'summary' => 'required|string|max:50',  //簡短介紹
                    'introduction' => 'required|string',   //說明
                    'tags' => 'required|string|max:20',
                    'version' => ['required', 'string', 'max:20', 'regex:/^[0-1]\.[0-9]*\.[0-9]$/'],
                    'changelog' => 'required|string',  //更新異動
                ]);

                if ($extension == 'apk') {
                    $path = Storage::putFileAs('public/file/android', $file, $file_name);
                    apps::insert([
                        'appName' => $request->appName,
                        'memberId' => '1',
                        'summary' => $request->summary,
                        'introduction' => $request->introduction,
                        'imgId' => '1',
                        'categoryId' => '1',
                        'tags' => $request->tags,
                        'device' => 'android',
                        'version' => $request->version,
                        'changelog' => $request->changelog,
                        'fileURL' => $path,
                    ]);
                    return response()->json(["boolean" => "True"]);
                } else if ($extension == 'ipa') {
                    $path = Storage::putFileAs('public/file/ios', $file, $file_name);
                    apps::insert([
                        'appName' => $request->appName,
                        'memberId' => '1',
                        'summary' => $request->summary,
                        'introduction' => $request->introduction,
                        'imgId' => '1',
                        'categoryId' => '1',
                        'tags' => $request->tags,
                        'device' => 'ios',
                        'version' => $request->version,
                        'changelog' => $request->changelog,
                        'fileURL' => $path,
                    ]);
                    return response()->json(["boolean" => "True"]);
                } else return response()->json(["boolean" => "False"]);
            }
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
