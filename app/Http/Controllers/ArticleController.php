<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Article;
use App\Http\Resources\Article as ArticleResource;

class ArticleController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $articles = Article::orderBy('created_at', 'desc')->paginate(5);
        return ArticleResource::collection($articles);
    }

    public function indexFiltered($user)
    {
        $articles = Article::where('author', $user)->orderBy('created_at', 'desc')->paginate(5);
        return ArticleResource::collection($articles);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $article = 
            $request->isMethod('put') ?
            Article::findOrFail($request->id) : new Article;

        $article->id = $request->input('id');
        $article->title = $request->input('title');
        $article->body = $request->input('body');
        $article->author = $request->input('author');
        $article->editor = $request->input('editor');
        $article->img_urls = $request->input('imgUrls');
        if ($request->isMethod('post'))
            $article->view_count = 0;

        if ($article->save())
            return new ArticleResource($article);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $article = Article::findOrFail($id);
        return new ArticleResource($article);
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
        $article = Article::findOrFail($id);
        if ($article->delete())
            return new ArticleResource($article);
    }

    public function updateViewCount(Request $request, $id)
    {
        $article = Article::findOrFail($id);
        ++$article->view_count;
        if ($article->save())
            return new ArticleResource($article);
    }
}
