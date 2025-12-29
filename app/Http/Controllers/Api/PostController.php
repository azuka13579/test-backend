<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Http\Requests\PostRequest;
use App\Http\Resources\PostResource;
use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;


class PostController extends Controller
{
    use AuthorizesRequests;
   public function index(Request $request) {
    // 1. Tambahkan withCount agar data likes/comments juga terhitung
    $query = Post::with('author')
                 ->withCount(['likes', 'comments'])
                 ->latest();

    // 2. Perbaiki logic search
    $query->when($request->search, function ($q, $search) {
        $q->where('title', 'ilike', "%{$search}%")
          ->orWhere('content', 'ilike', "%{$search}%");
    });

    // 3. Pastikan pagination ada defaultnya (biar gak null)
    $perPage = $request->pagination ?? 10;
    $posts = $query->paginate($perPage);

    return PostResource::collection($posts)->additional([
        'status' => 'Success',
        'message' => 'List of Post'
    ]);
}

 public function userPost(Request $request) {
    // 1. Tambahkan withCount agar data likes/comments juga terhitung
    $query = Post::with('author')->where('user_id', $request->userid)
                 ->withCount(['likes', 'comments'])
                 ->latest();

    // 2. Perbaiki logic search
    $query->when($request->search, function ($q, $search) {
        $q->where('title', 'ilike', "%{$search}%")
          ->orWhere('content', 'ilike', "%{$search}%");
    });

    // 3. Pastikan pagination ada defaultnya (biar gak null)
    $perPage = $request->pagination ?? 10;
    $posts = $query->paginate($perPage);

    return PostResource::collection($posts)->additional([
        'status' => 'Success',
        'message' => 'List of Post'
    ]);
}

   public function store(PostRequest $request)
{

    $this->authorize('create', Post::class);


    $validatedData = $request->validated();

    // 2. Handle Upload Gambar (Jika ada)
    if ($request->hasFile('image')) {
        // Simpan ke folder 'public/posts' dan ambil path-nya
        // Pastikan sudah run: php artisan storage:link
        $imagePath = $request->file('image')->store('posts', 'public');
        $validatedData['image_url'] = $imagePath;
    }

    // 3. Inject User ID dari User yang sedang login (LEBIH AMAN)
    $validatedData['user_id'] = $request->user()->id;

    // 4. Create Post
    $post = Post::create($validatedData);

    if(!$post){
        return response()->json([
            'status' => 'Error',
            'message' => 'Gagal membuat post'
        ], 500);
    }

    return (new PostResource($post))->additional([
        'status' => 'Success',
        'message' => 'Post berhasil ditambahkan',
    ]);
}

    public function show($id){
        $post = Post::with(['author','comments'])->find($id);

        if(!$post){
            return response()->json([
                'status' => 'Error',
                'message' => 'post tidak ditemukan'
            ], 500);
        }

        return (new PostResource($post))->additional([
            'status' => 'Success',
            'data' => $post
        ]);
    }

  public function update(PostRequest $request, $id)
{
    // 1. Cari Post berdasarkan ID
    $post = Post::find($id);

    $this->authorize('update', $post);


    // Cek jika post tidak ditemukan
    if (!$post) {
        return response()->json([
            'status' => 'Error',
            'message' => 'Post tidak ditemukan'
        ], 404);
    }

    // (Opsional) Cek Authorization: Pastikan yang edit adalah pemilik post
    if ($request->user()->id !== $post->user_id) {
        return response()->json([
            'status' => 'Error',
            'message' => 'Anda tidak memiliki akses untuk mengedit post ini'
        ], 403);
    }

    $validatedData = $request->validated();

    // 2. Handle Upload Gambar Baru (Jika ada)
    if ($request->hasFile('image')) {
        
        // A. Hapus gambar lama jika ada di database & storage
        if ($post->image_url && Storage::disk('public')->exists($post->image_url)) {
            Storage::disk('public')->delete($post->image_url);
        }

        // B. Simpan gambar baru
        $imagePath = $request->file('image')->store('posts', 'public');
        $validatedData['image_url'] = $imagePath;
    }

    // Catatan: Biasanya 'user_id' TIDAK di-update saat edit, 
    // jadi kita tidak perlu inject user_id lagi seperti di function store.

    // 3. Update Post
    $post->update($validatedData);

    return (new PostResource($post))->additional([
        'status' => 'Success',
        'message' => 'Post berhasil diperbarui',
    ]);
}

     public function destroy($id){


         
         $post = Post::find($id);
         
         $this->authorize('delete', $post);


        if(!$post){
            return response()->json([
                'status' => 'Error',
                'message' => 'Post tidak ditemukan'
            ], 500);
        }

        $post->delete();

        return response()->json([
            'status' => 'Success',
            'message' => 'Post berhasil dihapus anjayyy',
            'data' => $post
        ], 201);
    }


}



