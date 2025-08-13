document.addEventListener("DOMContentLoaded", function () {
  ymaps.ready(function () {
    // 1. Создаем карту
    const map = new ymaps.Map("map", {
      center: [55.76, 37.64], // Москва
      zoom: 12,
    });

    // 2. Создаем менеджер объектов с кластеризацией
    const objectManager = new ymaps.ObjectManager({
      clusterize: true,
      gridSize: 64,
      clusterDisableClickZoom: true,
      clusterIconLayout: "default#pieChart", // Визуализация кластеров
    });

    map.geoObjects.add(objectManager);

    // 3. Функция для загрузки кафе из OpenStreetMap
    function loadCafes(bbox) {
      const [south, west, north, east] = bbox;
      const url = `https://overpass-api.de/api/interpreter?data=[out:json];node[amenity=cafe](${south},${west},${north},${east});out;`;

      // Очищаем текущие объекты
      objectManager.removeAll();

      fetch(url)
        .then((response) => response.json())
        .then((data) => {
          const features = data.elements
            .filter(
              (element) => element.type === "node" && element.lat && element.lon
            )
            .slice(0, 10)
            .map((element) => ({
              type: "Feature",
              id: element.id,
              geometry: {
                type: "Point",
                coordinates: [element.lat, element.lon], // Исправлено: [широта, долгота] для Yandex Maps
              },
              properties: {
                hintContent: element.tags?.name || "Кафе",
                balloonContent:
                  (element.tags?.name || "Кафе") +
                  "<br>Координаты: " +
                  [element.lat, element.lon].join(", "),
              },
              options: {
                preset: "islands#redDotIcon",
              },
            }));

          // Добавляем в objectManager
          objectManager.add({
            type: "FeatureCollection",
            features: features,
          });
        })
        .catch((error) => console.error("Ошибка загрузки данных:", error));
    }

    // 6. Загружаем первые кафе
    loadCafes([55.7, 37.5, 55.8, 37.7]);

    // 7. Обновляем точки при изменении области
    map.events.add("boundschange", function (e) {
      const bounds = e.get("newBounds");
      loadCafes([
        bounds[0][0], // south
        bounds[0][1], // west
        bounds[1][0], // north
        bounds[1][1], // east
      ]);
    });

    // 8. Настройка кластеров
    objectManager.clusters.options.set({
      preset: "islands#invertedVioletClusterIcons",
      clusterDisableClickZoom: false,
      clusterOpenBalloonOnClick: true,
    });
  });
});
