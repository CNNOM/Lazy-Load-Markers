document.addEventListener("DOMContentLoaded", function () {
  console.log("asdasd");

  ymaps.ready(init);

  let placemarks = [];

  function init() {
    const map = new ymaps.Map("map", {
      center: [55.751574, 37.573856],
      zoom: 10,
      controls: ["zoomControl"],
    });

    function loadPoints(bounds) {
      const params = {
        minLat: bounds[0][0],
        minLng: bounds[0][1],
        maxLat: bounds[1][0],
        maxLng: bounds[1][1],
        zoom: map.getZoom(),
      };

      BX.ajax({
        url: "/local/components/dterra/points/ajax.php",
        data: params,
        method: "POST",
        dataType: "json",
        onsuccess: function (data) {
          placemarks.forEach((placemark) => {
            map.geoObjects.remove(placemark);
          });
          placemarks = [];

          data.forEach((point) => {
            const placemark = new ymaps.Placemark(
              [point.LATITUDE, point.LONGITUDE],
              {
                balloonContent: point.NAME,
                hintContent: point.DESCRIPTION || "",
              },
              {
                preset: "islands#redDotIcon",
                balloonCloseButton: true,
              }
            );
            map.geoObjects.add(placemark);
            placemarks.push(placemark);
          });

          console.log("Загружено точек: " + data.length);
        },
        onfailure: function (error) {
          console.log("Ошибка загрузки точек: " + error.statusText);
        },
      });
    }

    let debounceTimer;
    map.events.add("boundschange", function () {
      clearTimeout(debounceTimer);
      debounceTimer = setTimeout(function () {
        const bounds = map.getBounds();
        loadPoints(bounds);
        logBounds(bounds);
      }, 500);
    });

    function logBounds(bounds) {
      try {
        const containerSize = map.container.getSize();
        const distance = ymaps.coordSystem.geo.getDistance(
          [bounds[0][0], bounds[0][1]],
          [bounds[1][0], bounds[1][1]]
        );

        const message = `Область: ${(distance / 1000).toFixed(
          2
        )}км, Масштаб: ${map.getZoom()}`;
        console.log(message);
      } catch (e) {
        console.log("Ошибка: " + e.message);
      }
    }

    loadPoints(map.getBounds());
  }
});
