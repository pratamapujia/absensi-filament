<div>
  <div class="container mx-auto max-w-sm">
    <div class="bg-white p-6 rounded-lg shadow-lg">
      <div class="grid grid-cols-1 gap-6 mb-6">
        <div>
          <h2 class="text-2xl font-bold mb-2">Informasi Pegawai</h2>
          <div class="bg-gray-100 p-4 rounded-lg">
            <p><strong>Nama Pegawai : </strong> {{ Auth::user()->name }}</p>
            <p><strong>Kantor : </strong> {{ $scedule->office->name }}</p>
            <p><strong>Shift : </strong> {{ $scedule->shift->name }} ({{ $scedule->shift->start_time }} - {{ $scedule->shift->end_time }}) WIB</p>
            @if ($scedule->is_wfa)
              <p class="text-green-500"><strong>Status : WFA</strong></p>
            @else
              <p><strong>Status : WFO</strong></p>
            @endif
          </div>
          <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-4">
            <div class="bg-gray-100 rounded-lg p-4">
              <h4 class="text-l font-bold mb-2">Waktu Datang</h4>
              <p><strong>{{ $attendance ? $attendance->start_time : '--:--' }}</strong></p>
            </div>
            <div class="bg-gray-100 rounded-lg p-4">
              <h4 class="text-l font-bold mb-2">Waktu Pulang</h4>
              <p><strong>{{ $attendance ? $attendance->end_time : '--:--' }}</strong></p>
            </div>
          </div>
        </div>
        <div>
          <h2 class="text-2xl font-bold mb-2">Informasi Pegawai</h2>
          <div id="map" class="mb-4 rounded-lg border border-gray-300" wire:ignore>
          </div>
          @if (session()->has('error'))
            <div style="color: red; padding: 10px; border: 1px solid red; border-radius: 10px; background-color: #fdd">
              {{ session('error') }}
            </div>
          @endif
          <form class="row g-3 mt-3" wire:submit='store' enctype="multipart/form-data">
            <button type="button" onclick="tagLocation()" class="px-4 py-2 bg-blue-500 text-white rounded">Tag Location</button>
            @if ($insideRadius)
              <button type="submit" class="px-4 py-2 bg-green-500 text-white rounded">Submit Presensi</button>
            @endif
          </form>
        </div>
      </div>
    </div>
  </div>
  <script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
  <script>
    let map;
    let lat;
    let lng;
    const office = [{{ $scedule->office->latitude }}, {{ $scedule->office->longitude }}];
    const radius = {{ $scedule->office->radius }};
    let component;
    let marker;

    document.addEventListener('livewire:initialized', function() {
      component = @this;
      map = L.map('map').setView([{{ $scedule->office->latitude }}, {{ $scedule->office->longitude }}], 17);
      L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png').addTo(map);
      const circle = L.circle(office, {
        color: 'red',
        fillColor: '#f03',
        fillOpacity: 0.5,
        radius: {{ $scedule->office->radius }}
      }).addTo(map);
    })

    function tagLocation() {
      if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition((position) => {
          lat = position.coords.latitude;
          lng = position.coords.longitude;
          if (marker) {
            map.removeLayer(marker);
          }
          marker = L.marker([lat, lng]).addTo(map);
          map.setView([lat, lng], 17);

          if (isWhithinRadius(lat, lng, office, radius)) {
            component.set('insideRadius', true);
            component.set('latitude', lat);
            component.set('longitude', lng);
          }
        });
      } else {
        alert('Geolocation is not supported by this browser.');
      }
    }

    function isWhithinRadius(lat, lng, office, radius) {
      const is_wfa = {{ $scedule->is_wfa }};
      if (is_wfa) {
        return true;
      } else {
        let distance = map.distance([lat, lng], office);
        return distance <= radius;
      }
    }
  </script>
</div>
