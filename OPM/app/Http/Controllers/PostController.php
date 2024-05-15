<?php

namespace App\Http\Controllers;

use App\Models\Post;

use Illuminate\View\View;

use App\Http\Controllers\Controller;

use Illuminate\Http\RedirectResponse;

use Illuminate\Http\Request;

use Illuminate\Support\Facades\Redirect;

use Illuminate\Support\Facades\Storage;

class PostController extends Controller
{
    /**
     * index
     * 
     * @return View
     */
    public function index(): View
    {
        $posts = Post::latest()->paginate(5);

        return view('posts.index', compact('posts'));
    }

    public function create(): View
    {
        return view('posts.create');
    }

    public function store(Request $request): RedirectResponse
    {
            //validate form
        $this->validate($request, [
            'image' => 'required|image|mimes:jpeg,jpg,png|max:4048', 
            'nama' => 'required|min:1',
            'jurusan' => 'required|min:1',
            'nohp' => 'required|min:1',
            'email' => 'required|min:1',
            'alamat' => 'required|min:1',
        ]);
            //upload image
            $image = $request->file('image');
            $image->storeAs('public/posts', $image->hashName());
            
            //create post 
            Post::create([
            'image' => $image->hashName(), 
            'nama' => $request->nama, 
            'jurusan' => $request->jurusan,
            'nohp' => $request->nohp,
            'email' => $request->email,
            'jurusan' => $request->jurusan,
            'alamat' => $request->alamat
            ]);
        //redirect to index
        return redirect()->route('posts.index')->with(['success' =>  'Berhasil Disimpan!']);
    }

    //menampilkan
    public function show(string $id):View
    {
        $post = Post::findOrFail($id);

        return view('posts.show', compact('post'));
    }

    //hapus
    public function destroy($id) : RedirectResponse
    {
        $post = Post::findOrFail($id);
    
        Storage::delete('public/posts/' . $post->image);
    
        $post->delete();
    
        return redirect()->route('posts.index')->with(['success' => 'Data Berhasil Dihapus']);

    }

    //edit dan update
    public function edit(string $id): View
    {
        $post = Post::findOrFail($id);

        return view('posts.edit', compact('post'));
    }
    
    public function update(Request $request, $id): RedirectResponse
    {
        $this->validate($request, [
            'image' => 'required|image|mimes:jpeg,jpg,png|max:4048', 
            'nama' => 'required|min:1',
            'jurusan' => 'required|min:1',
            'nohp' => 'required|min:1',
            'email' => 'required|min:1',
            'alamat' => 'required|min:1',
        ]);
    
        $post = Post::findOrFail($id);
    
        if ($request->hasFile('image'))
        {
            // Upload image
            $image = $request->file('image');
            $image->storeAs('public/posts', $image->hashName());
            
            // Delete old image
            Storage::delete('public/posts/' . $post->image);
            
            $post->update([
                'image' => $image->hashName(), 
                'nama' => $request->nama, 
                'jurusan' => $request->jurusan,
                'nohp' => $request->nohp,
                'email' => $request->email,
                'jurusan' => $request->jurusan,
                'alamat' => $request->alamat
            ]);
        }
        else {
            $post->update([
                'nama' => $request->nama, 
                'jurusan' => $request->jurusan,
                'nohp' => $request->nohp,
                'email' => $request->email,
                'jurusan' => $request->jurusan,
                'alamat' => $request->alamat
            ]);
        }
        // Redirect to index
        return redirect()->route('posts.index')->with(['success' =>  'Berhasil Disimpan!']);
    }
    
}