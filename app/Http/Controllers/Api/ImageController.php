<?php

namespace App\Http\Controllers\Api;
 
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Validator;
use App\Http\Helper\ResponseBuilder;
use Illuminate\Support\Facades\DB; 
use Yajra\Datatables\Datatables;
use Illuminate\Support\Str; 

use Intervention\Image\ImageManagerStatic as Image;


class ImageController extends Controller
{ 

    public function generateImage(Request $request){
        // Validasi input
        $request->validate([
            'tipe_kegiatan' => 'required|string'
        ]); 

        $backgroundImage = Image::make(public_path('pamplet.png'))->fit(900, 900, function ($constraint) {
            $constraint->aspectRatio();
            $constraint->upsize();
        });  

        // Muat foto tambahan
        $photo = Image::make($request->input('foto_narsum'))->fit(300, 300, function ($constraint) {
            $constraint->aspectRatio();
            $constraint->upsize();
        });


        // Buat gambar
        $img = Image::canvas(900, 900, '#ffffff');
        $img->insert($backgroundImage, 'center');
        $img->insert($photo, 'top-right', 100, 200);

        // Tambahkan teks ke gambar
        $img->text($request->input('tipe_kegiatan'), 65, 300, function ($font) {
            $font->file(public_path('YesevaOne-Regular.ttf')); // Pastikan kamu memiliki font ini
            $font->size(55);
            $font->color('#ffffff');
            $font->align('left');
            $font->valign('left');
        });

        $img->text($this->wrapText($request->input('nm_kegiatan'), public_path('YesevaOne-Regular.ttf'), 20, 300), 65, 310, function ($font) {
            $font->file(public_path('YesevaOne-Regular.ttf')); // Pastikan kamu memiliki font ini
            $font->size(55);
            $font->color('#C81EFF');
            $font->align('left');
            $font->valign('left');
        });

        
        $img->text($this->wrapText($request->input('sub_tema'), public_path('GolosText-Regular.ttf'), 20, 300), 65, 450, function ($font) {
            $font->file(public_path('GolosText-Regular.ttf')); // Pastikan kamu memiliki font ini
            $font->size(25);
            $font->color('#ffffff');
            $font->align('left');
            $font->valign('left');
        });

        
        // KETERANGAN
        $img->text($request->input('narsum'), 65, 590, function ($font) {
            $font->file(public_path('GolosText-Bold.ttf')); // Pastikan kamu memiliki font ini
            $font->size(33);
            $font->color('#C16D5C');
            $font->align('left');
            $font->valign('left');
        });

        $img->text($request->input('ket_narsum'), 65, 630, function ($font) {
            $font->file(public_path('GolosText-Regular.ttf')); // Pastikan kamu memiliki font ini
            $font->size(25);
            $font->color('#ffffff');
            $font->align('left');
            $font->valign('left');
        });

        $img->text($request->input('ruangan'), 65, 670, function ($font) {
            $font->file(public_path('GolosText-Regular.ttf')); // Pastikan kamu memiliki font ini
            $font->size(25);
            $font->color('#ffffff');
            $font->align('left');
            $font->valign('left');
        });

        $img->text($request->input('tanggal'), 65, 710, function ($font) {
            $font->file(public_path('GolosText-Regular.ttf')); // Pastikan kamu memiliki font ini
            $font->size(25);
            $font->color('#ffffff');
            $font->align('left');
            $font->valign('left');
        });

        $img->text(''.$request->input('waktu').' - '.$request->input('waktu_end').'', 65, 760, function ($font) {
            $font->file(public_path('GolosText-Regular.ttf')); // Pastikan kamu memiliki font ini
            $font->size(35);
            $font->color('#ffffff');
            $font->align('left');
            $font->valign('left');
        });

        $img->text($request->input('contact'), 120, 840, function ($font) {
            $font->file(public_path('GolosText-Regular.ttf')); // Pastikan kamu memiliki font ini
            $font->size(25);
            $font->color('#ffffff');
            $font->align('left');
            $font->valign('left');
        });
        
        $fileName = ''.$request->input('tipe_kegiatan').'-'.$request->input('narsum').'-('.$request->input('tanggal').')';

        // Simpan atau kirim gambar sebagai response
        $img->save(storage_path('app/public/generatePamplet/'.$fileName.'.png'));

        // Return response dengan URL gambar
        return response()->json([
            'status' => 200,
            'message' => 'Image generated successfully!',
            'image_url' => url('storage/generatePamplet/'.$fileName.'.png'),
        ]);
    }


    private function wrapText($text, $fontPath, $fontSize, $maxWidth)
    {
        $wrappedText = '';
        $words = explode(' ', $text);
        $line = '';

        foreach ($words as $word) {
            // Tambahkan kata ke baris untuk pengujian
            $testLine = $line === '' ? $word : $line . ' ' . $word;

            // Buat gambar sementara untuk mengukur lebar teks
            $box = imagettfbbox($fontSize, 0, $fontPath, $testLine);
            $textWidth = abs($box[4] - $box[0]); // Hitung lebar teks dari bounding box

            // Cek apakah baris yang diuji lebih lebar dari maksimum lebar yang diizinkan
            if ($textWidth > $maxWidth) {
                // Jika iya, tambahkan baris saat ini ke teks yang dibungkus dan mulai baris baru
                $wrappedText .= ($line !== '' ? $line . "\n" : '') . $word;
                $line = $word;
            } else {
                // Jika tidak, lanjutkan menambahkan kata ke baris
                $line = $testLine;
            }
        }

        // Tambahkan baris terakhir ke teks yang dibungkus
        $wrappedText .= ($line !== '' ? "\n" . $line : '');

        return $wrappedText;
    }

}