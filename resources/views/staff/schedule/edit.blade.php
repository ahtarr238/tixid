@extends('templates.app')

@section('content')
    <div class="container my-5">
        <form method="POST" action="{{ route('staff.schedules.update', $schedule['id']) }}">
            @csrf
            @method('PATCH')
            <div class="modal-body">
                <div class="mb-3">
                    <label for="cinema_id" class="col-form-label">Bioskop:</label>
                    <input type="hidden" name="cinema_id" id="cinema_id" value="{{ $schedule['cinema']['id'] }}">
                    <p class="form-control-static">{{ $schedule['cinema']['name'] }}</p>
                    @error('cinema_id')
                        <small class="text-danger">{{ $message }}</small>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="movie_id" class="col-form-label">Film:</label>
                    <input type="hidden" name="movie_id" id="movie_id" value="{{ $schedule['movie']['id'] }}">
                    <p class="form-control-static">{{ $schedule['movie']['title'] }}</p>
                    @error('movie_id')
                        <small class="text-danger">{{ $message }}</small>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="price" class="form-label">Harga:</label>
                    <input value="{{ $schedule['price'] }}" type="number" name="price" id="price"
                        class="form-control @error('price') is-invalid @enderror">
                    @error('price')
                        <small class="text-danger">{{ $message }}</small>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="hours" class="form-label">Jam Tayang</label><br>
                    @foreach ($schedule['hours'] as $index => $hour)
                        <div class="d-flex align-items-center hour-item mb-3">
                            <input type="time" name="hours[]" id="hours" class="form-control"
                                value="{{ $hour }}">
                            @if ($index > 0)
                                <i class="fa-solid fa-circle-xmark text-danger my-2 mx-2"
                                    style="font-size: 1.5rem; cursor: pointer"
                                    onclick="this.closest('.hour-item').remove()"></i>
                            @endif
                        </div>
                    @endforeach
                    <div id="additionalInput"></div>
                    <span class="text-primary my-3" style="cursor: pointer" onclick="addInput()"> + Tambah Jam</span>
                    @if ($errors->has('hours.*'))
                        <br>
                        <small class="text-danger">{{ $errors->first('hours.*') }}</small>
                    @endif
                </div>
            </div>

            <button type="submit" class="btn btn-primary">Kirim</button>
        </form>
    </div>
@endsection

@push('script')
    <script>
        function addInput() {
            const content = `
                <div class="d-flex align-items-center hour-additional mb-3">
                    <input type="time" name="hours[]" class="form-control" value="">
                    <i class="fa-solid fa-circle-xmark text-danger my-2 mx-2" 
                    style="font-size: 1.5rem; cursor: pointer" 
                    onclick="this.closest('.hour-additional').remove()"></i>
                </div>
            `;
            document.querySelector('#additionalInput').innerHTML += content;
        }
    </script>
@endpush
