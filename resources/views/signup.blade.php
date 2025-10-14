@extends('templates.app')

@section('content')
    <div class="w-75 d-block mx-auto my-5">
        <form method="POST" action="{{ route('sign_up') }}">
            {{-- token : syarat biar formulir bisa mengirim data ke server / ke kontroller --}}
            @csrf
            <!-- 2 column grid layout with text inputs for the first and last names -->
            <div class="row mb-4">
                <div class="col">
                    <div data-mdb-input-init class="form-outline">
                        {{-- setiap input hrs punya name : identitas data agar bisa diambil ke database --}}
                        <input type="text" name="first_name" value="{{old('first_name')}}" id="form3Example1"
                            class="form-control
                        @error('first_name') is-invalid @enderror " />
                        {{-- @error : mengambil error validasi sesuai nama yang dipanggil --}}
                        <label class="form-label" for="form3Example1">First name</label>
                    </div>
                    {{-- @error : mengambil error validasi sesuai nama yang dipanggil --}}
                    @error('first_name')
                        <small class="text-danger">{{ $message }}</small>
                    @enderror
                </div>
                <div class="col">
                    <div data-mdb-input-init class="form-outline">
                        <input type="text" name="last_name" value="{{old('first_name')}}" id="form3Example2"
                            class="form-control @error('last_name') is-invalid @enderror " />
                        <label class="form-label" for="form3Example2">Last name</label>
                    </div>
                    @error('last_name')
                        <small class="text-danger">{{ $message }}</small>
                    @enderror
                </div>
            </div>

            <!-- Email input -->
            <div data-mdb-input-init class="form-outline mb-4">
                <input type="email" name="email" value="{{old('first_name')}}" id="form3Example3"
                    class="form-control @error('email') is-invalid @enderror" />
                <label class="form-label" for="form3Example3">Email address</label>
            </div>
            @error('email')
                <small class="text-danger">{{ $message }}</small>
            @enderror

            <!-- Password input -->
            <div data-mdb-input-init class="form-outline mb-4">
                <input type="password" name="password" value="{{old('first_name')}}" id="form3Example4"
                    class="form-control @error('password') is-invalid @enderror" />
                <label class="form-label" for="form3Example4">Password</label>
            </div>
            @error('password')
                <small class="text-danger">{{ $message }}</small>
            @enderror

            <!-- Checkbox -->
            <div class="form-check d-flex justify-content-center mb-4">
                <input class="form-check-input me-2" type="checkbox" value="" id="form2Example33" checked />
                <label class="form-check-label" for="form2Example33">
                    Subscribe to our newsletter
                </label>
            </div>

            <!-- Submit button -->
            <button data-mdb-ripple-init type="submit" class="btn btn-primary btn-block mb-4">Sign up</button>

            <!-- Register buttons -->
            <div class="text-center">
                <p>or sign up with:</p>
                <button data-mdb-ripple-init type="button" class="btn btn-secondary btn-floating mx-1">
                    <i class="fab fa-facebook-f"></i>
                </button>

                <button data-mdb-ripple-init type="button" class="btn btn-secondary btn-floating mx-1">
                    <i class="fab fa-google"></i>
                </button>

                <button data-mdb-ripple-init type="button" class="btn btn-secondary btn-floating mx-1">
                    <i class="fab fa-twitter"></i>
                </button>

                <button data-mdb-ripple-init type="button" class="btn btn-secondary btn-floating mx-1">
                    <i class="fab fa-github"></i>
                </button>
            </div>
        </form>
    </div>
@endsection