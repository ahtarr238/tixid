{{-- memanggil file template --}}
@extends('templates.app')
{{-- mengisi yield  --}}

@section('content')

<style>

.main-footer {
    background-color: #0d1a3c;
    color: #fff;
    padding: 40px 0;
    font-family: Arial, sans-serif;
    margin-top: 50px;
}


.main-footer .company-info .logo {
    max-height: 30px;
    margin-bottom: 15px; 
}

.main-footer h3,
.main-footer h4 {
    color: #fff;
    margin-bottom: 15px;
}

.main-footer p {
    font-size: 14px;
    line-height: 1.5;
    margin-bottom: 10px;
}

.container {
    max-width: 1100px;
    margin: 0 auto;
    padding: 0 20px;
}

.footer-columns {
    display: flex;
    justify-content: space-between;
    flex-wrap: wrap;
    gap: 30px;
}

.footer-section {
    flex: 1;
    min-width: 150px;
}

.footer-section ul {
    list-style: none;
    padding: 0;
    margin: 0;
}

.footer-section li a {
    color: #fff;
    text-decoration: none;
    font-size: 14px;
    line-height: 2.2;
    transition: color 0.3s;
}

.footer-section li a:hover {
    color: #007bff;
}

.social-icons {
    display: flex;
    gap: 15px;
}

.social-icons li a {
    font-size: 20px;
}
</style>


    @if (Session::get('success'))
        {{--Auth::user() : mengambil data pengguna yg login --}}
        {{-- format : Aith::user()->column_di_fillable --}}
        <div class="alert alert-success w-100">{{ Session::get('success')}} <b>Selamat Datang, {{ Auth::user()->name }}</b> </div>
@endif
    @if (Session::get('logout'))
        <div class="alert alert-warning w-100">{{Session::get('logout')}}</div>
    @endif
    <div class="dropdown">
        <button class="btn btn-light dropdown-toggle w-100 d-flex align-items-center " type="button" id="dropdownMenuButton"
            data-mdb-dropdown-init data-mdb-ripple-init aria-expanded="false">
            <i class="fa-solid fa-location-dot me-2 text"></i> <b>Bogor</b> 
        </button>
        <ul class="dropdown-menu w-100" aria-labelledby="dropdownMenuButton">
            <li><a class="dropdown-item text-center" href="#">#</a></li>
            <li><a class="dropdown-item text-center" href="#">#</a></li>
            <li><a class="dropdown-item text-center" href="#">#</a></li>
        </ul>
    </div>
    <div id="carouselExampleIndicators" class="carousel slide" data-mdb-ride="carousel" data-mdb-carousel-init>
        <div class="carousel-indicators">
            <button type="button" data-mdb-target="#carouselExampleIndicators" data-mdb-slide-to="0" class="active"
                aria-current="true" aria-label="Slide 1"></button>
            <button type="button" data-mdb-target="#carouselExampleIndicators" data-mdb-slide-to="1"
                aria-label="Slide 2"></button>
            <button type="button" data-mdb-target="#carouselExampleIndicators" data-mdb-slide-to="2"
                aria-label="Slide 3"></button>
        </div>
        <div class="carousel-inner">
            <div class="carousel-item active">
                <img src="https://wallpapercave.com/wp/fWnWpHj.jpg" class="d-block w-100" style="height: 400px"
                    alt="Wild Landscape" />
            </div>
            <div class="carousel-item">
                <img src="https://th.bing.com/th/id/R.f141845566c5f6863287fe27ad039f37?rik=kH38r0TRy%2b6AGg&riu=http%3a%2f%2fwww.impawards.com%2f2014%2fposters%2finterstellar_ver7_xlg.jpg&ehk=OK8Rxe7YfFYjZCf9jYNVsANdHPxzY4nm%2fVdY7W1R070%3d&risl=&pid=ImgRaw&r=0"
                    class="d-block w-100" style="height: 400px" alt="Camera" />
            </div>
            <div class="carousel-item">
                <img src="https://i.ytimg.com/vi/BmbpHzonLOs/maxresdefault.jpg" class="d-block w-100" style="height: 400px"
                    alt="Exotic Fruits" />
            </div>
        </div>
        <button class="carousel-control-prev" type="button" data-mdb-target="#carouselExampleIndicators"
            data-mdb-slide="prev">
            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
            <span class="visually-hidden">Previous</span>
        </button>
        <button class="carousel-control-next" type="button" data-mdb-target="#carouselExampleIndicators"
            data-mdb-slide="next">
            <span class="carousel-control-next-icon" aria-hidden="true"></span>
            <span class="visually-hidden">Next</span>
        </button>
    </div>
    <div class="d-flex justify-content-between container mt-4 ">
        <div class="d-flex ailng-items-center gap-2 mt-1">
            <i class="fa-solid fa-clapperboard mt-1"></i>
            <h5">Sedang Tayang</h5>
        </div>
        <div>
            <a href="{{route('home.movies')}}" class="btn btn-warning rounded-pill">Semua <i class="fa-solid fa-angle-right"></i></a>
        </div>
    </div>
    <div class="d-flex gap-2 container">
        <button type="button" class="btn btn-outline-primary rounded-pill" data-mdb-ripple-init
            data-mdb-ripple-color="dark">SEMUA FILM</button>
        <button type="button" class="btn btn-outline-secondary rounded-pill" data-mdb-ripple-init
            data-mdb-ripple-color="dark">XXI</button>
        <button type="button" class="btn btn-outline-secondary rounded-pill" data-mdb-ripple-init
            data-mdb-ripple-color="dark">IMAX</button>
        <button type="button" class="btn btn-outline-secondary rounded-pill" data-mdb-ripple-init
            data-mdb-ripple-color="dark">CINEPOLIS</button>
    </div>

    <div class="mt-3 d-flex justify-content-center container gap-2">
        @foreach ($movies as $item)
            <div class="card shadow-sm text-center" style="width: 15rem;">
                <img src="{{ asset('storage/' . $item['poster']) }}"class="card-img-top" style="height:300px; object-fit:cover;" alt="{{ $item['title']}}">
                <div class="card-body">
                    <h6 class="card-title mb-2">{{ $item['title']}}</h6>
                    <p class="small text-muted mb-2">{{ $item['genre']}}</p>
                    <a href="{{route('schedule.detail', $item['id'])}}" class="btn btn-primary btn-sm mx-2 mt-2">Beli tiket</a>
                    @if ($item->actived == 0)
                        <span class="badge bg-warning text-dark ms-2">Non-aktif</span>
                    @endif
                </div>
            </div>
        @endforeach
    </div>

<footer class="main-footer">
    <div class="container">
        <div class="footer-columns">
            <div class="footer-section company-info">
                <img src="https://asset.tix.id/wp-content/uploads/2021/10/TIXID_logo_inverse-300x82.png" alt="TIX ID Logo" class="logo">
                <p>Best App For Movie Lovers</p>
                <p>In Indonesia! Movie Entertainment Platform From Cinema To Online Movie Streaming Selections.</p>
            </div>

            <div class="footer-section nav-links">
                <h4>Now Showing</h4>
                <ul>
                    <li><a href="#">TIX NOW</a></li>
                    <li><a href="#">SPOTLIGHT</a></li>
                    <li><a href="#">VIDEO & TRAILERS</a></li>
                </ul>
            </div>

            <div class="footer-section site-links">
                <h4>Careers</h4>
                <ul>
                    <li><a href="#">Contact Us</a></li>
                    <li><a href="#">Privacy Policy</a></li>
                    <li><a href="#">Terms & Conditions</a></li>
                </ul>
            </div>

            <div class="footer-section social-links">
                <h4>TIX ID SUPPORT</h4>
                <p>E-MAIL: HELP@TIX.ID</p>
                <h4>FOLLOW US</h4>
                <ul class="social-icons">
                    <li><a href="#"><i class="fa-brands fa-instagram"></i></a></li>
                    <li><a href="#"><i class="fa-brands fa-facebook"></i></a></li>
                    <li><a href="#"><i class="fa-brands fa-twitter"></i></a></li>
                    <li><a href="#"><i class="fa-brands fa-pinterest"></i></a></li>
                </ul>
            </div>
        </div>
    </div>
</footer>

@endsection