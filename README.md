# Kép feltöltése 

<a href="">Laravel backend repo</a> <br>
<a href="">React frontend repo</a>

## Backend - Laravel 11

## 1. Laravel (11) telepítése és indítása

    composer create-project laravel/laravel kepfeltoltes_backend
    cd kepfeltoltes_backend
    php artisan serve

### 1.1. <a href="https://laravel.com/docs/11.x/sanctum#how-it-works"> Az SPA autentikáció elkészítése </a> <br>

    php artisan install:api

### 1.2. .env fájl konfigurálása a bootstrap/app.php fájlban

Itt mondjuk meg, hogy mely domainről fogadjuk a kéréseket

    APP_URL=http://localhost
    FRONTEND_URL=http://localhost:3000  

    SESSION_DOMAIN=localhost
    SANCTUM_STATEFUL_DOMAINS=localhost:3000

### 1.3. Sanctum Middleware beállítása 

    ->withMiddleware(function (Middleware $middleware) {
        //
        $middleware->statefulApi(); /*sanctum  instruct Laravel that incoming requests from your SPA can authenticate using Laravel's session cookies, while still allowing requests from third parties or mobile applications to authenticate using API tokens. */
    })

### 1.4. config/cors.php telepítése és beállítása

Telepítés: 

    php artisan config:publish cors

Beállítások: 

    'paths' => ['api/*', 'sanctum/csrf-cookie'],
    'supports_credentials' => true,

### 1.5 frontend oldalon az axios alapbeállításait is ehhez kell igazítani: 

    export default axios.create({
        baseURL:"http://localhost:8000",
        withCredentials:true,
        withXSRFToken :true, 
        
    })


## 2. Kepek modell és KepekController  készítése  a migrációs fájlokkal

    php artisan make:model Kepek -m

### Migrációs fájl

    public function up(): void
        {
            Schema::create('kepeks', function (Blueprint $table) {
                $table->id();
                $table->string('title');        
                $table->string('name')->nullable();
                $table->timestamps();
            });
            Kepek::create(['title'=>'Kép 1',"name"=>"kepek/_DSC3794.jpg"]);
            Kepek::create(['title'=>'Kép 1',"name"=>"kepek/_DSC3807.jpg"]);
        }

A képek helye a public mappában létrehozott kepek mappában lesz. Tegyünk oda a feltöltésben szereplő fájlokat!

### Kepek Modell

    class Kepek extends Model
    {
        use HasFactory;
        protected $fillable = [
            'title', 'name'
        ];
    }

### Adatbázis migrálása

    php artisan migrate:fresh

### KepekController 

1. A kérés validálásához a validate függvényt használjuk. Beállítjuk az elfogadott képformátumokat  és a feltölthető kép maximális méretét. 
2. Lekérjük a fájl nevét és kiterjesztését.   
3. Új, egyedi nevet generálunk, az eredeti kiterjesztés hozzáfűzzük a névhez. 
4. Átmozgatjuk a public mappa 'kepek' könyvtárába 
5. Létrehozzzuk az új objektumot az új fájl elérési úttal, illetve a címmel, majd mentjük az adatbázisba. 


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

## Route elkészítése az api.php fájlban

    Route::get('file-upload', [KepekController::class, 'index'])->name('file.upload');
    Route::post('file-upload', [KepekController::class, 'store'])->name('file.upload.store');









