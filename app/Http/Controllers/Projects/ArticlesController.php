<?php

namespace App\Http\Controllers\Projects;

use Inertia\Inertia;
use Auth;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Models\Project;
use App\Models\Article;

class ArticlesController extends Controller
{
    public function __construct()
    {
        $this->middleware('project.role:superadmin');
    }

    public function upload()
    {
		$folder = request('folder') ? request('folder') : 'articles';
		$url = request('file')->store($folder, 'static');

        $whitelist = array(
            '127.0.0.1',
            '::1'
        );
		
		if($url){
			$json = array(
				'uploaded' => true,
				'fileName' => basename($url),
				'fileNameFolder' => $folder.'/'.basename($url),
				'location' => !in_array($_SERVER['REMOTE_ADDR'], $whitelist) ? 'https://app.appetite.link/media/static/'.$url : 'http://app.appetitelink:8000/media/static/'.$url
			);

			return response()->json($json);
		}   
    }

    public function view()
    {
        $id = current_project()->id;

        $data['module'] = project_module('articles');
        $data['project'] = Project::with(['i18n','qr','sources','prizes','articles'])->find($id);

        return Inertia::render('Projects/Articles', $data);
    }

    public function store()
    {
        $id = current_project()->id;

        $article = Article::findOrNew(request('id'));
        $article->title = request('title');
        $article->slug = request('slug');
        $article->content = request('content');
        $article->content_secondary = request('content_secondary');
        $article->tags = request('tags');
        $article->image = request('image');
        $article->project_id = $id;
        $article->country = request('country') ? request('country')['code'] : null;
        $article->language = request('language') ? request('language')['code'] : null;

        if($article->save()){
            return redirect()->back()->with('status','Article '.request('id') ? 'updated' : 'created');
        }
    }

    public function actions()
    {
        if(request('delQuestion')):
            Article::find(request('delQuestion'))->delete();
            return redirect()->back()->with('status','Question deleted!');
        endif;
    }
}