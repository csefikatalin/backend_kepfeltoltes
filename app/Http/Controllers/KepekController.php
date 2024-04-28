<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Validator;

use App\Models\Kepek;

class KepekController extends Controller
{
    public function index()
    {
        $files = Kepek::latest()->get();
        return $files;
    }

    public function store(Request $request)
    {
       // a kérés validálásához a validate függvényt használjuk. Beállítjuk az elfogadott képformátumokat
       // és a feltölthető kép maximális méretét. 
        $request->validate([
            'title' => 'required',
            'name' =>  'image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);
        $file = $request->file('name');   // fájl nevének lekérése  
        $extension = $file->getClientOriginalName(); //kiterjesztés
        $imageName = time() . '.' . $extension; // a kép neve az időbéjegnek köszönhetően egyedi lesz. 
        $file->move(public_path('kepek'), $imageName); //átmozgatjuk a public mappa kepek könyvtárába 
        $kepek = new Kepek(); // Létrehozzuk a kép objektumot. 
        $kepek->name = 'kepek/' . $imageName; // megadjuk az új fájl elérési utját
        $kepek->title = $request->title; // megadjuk a kép címét
        $kepek->save(); //elmentjük

        return redirect()->route('file.upload')->with('success', 'Product created successfully.');
    }
}
