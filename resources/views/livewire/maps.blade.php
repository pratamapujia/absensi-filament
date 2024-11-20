<div class="grid grid-cols-1 dark:bg-gray-900 md:grid-cols-12 gap-4" wire:ignore>
  <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css">

  <div class="md:col-span-2 bg-white dark:bg-gray-800 shadow-md rounded-lg p-6">
    <div id="map" class="w-full" style="height: 75vh"></div>
  </div>

  <script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
  <script>
    document.addEventListener('livewire:initialized', function() {
      component = @this;
      let map = L.map('map').setView([-0.089275, 121.921327], 4.5);
      L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png').addTo(map);

      const markers = @json($attendances);

      markers.forEach((marker) => {
        const str = `Nama : ${marker.user.name} <br> Waktu Datang : ${marker.start_time} <br> Waktu Pulang : ${marker.end_time}`;
        L.marker([marker.start_latitude, marker.start_longitude]).addTo(map).bindPopup(str);
      })
    })
  </script>
</div>
