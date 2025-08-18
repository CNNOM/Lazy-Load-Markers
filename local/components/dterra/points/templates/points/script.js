document.addEventListener("DOMContentLoaded", function() {
  ymaps.ready(init);

  function init() {
    const map = new ymaps.Map("map", {
      center: [55.751574, 37.573856],
      zoom: 10,
      controls: ["zoomControl"],
    });

    // Создаем ObjectManager с ленивой загрузкой
    const objectManager = new ymaps.LoadingObjectManager('/local/components/dterra/points/objects.php?bbox=%b', {
      clusterize: true,
      gridSize: 100,
      clusterDisableClickZoom: true,
      clusterIconLayout: 'default#pieChart',
      clusterIconPieChartRadius: 25,
      clusterIconPieChartCoreRadius: 10,
      clusterIconPieChartStrokeWidth: 3
    });

    // Настройка внешнего вида кластеров
    objectManager.clusters.options.set({
      preset: 'islands#invertedVioletClusterIcons',
      clusterDisableClickZoom: true,
      clusterHideIconOnBalloonOpen: false,
      geoObjectHideIconOnBalloonOpen: false
    });

    // Настройка внешнего вид меток
    objectManager.objects.options.set({
      preset: 'islands#redDotIcon',
      balloonCloseButton: true
    });

    // Добавляем обработчик клика по метке
    objectManager.objects.events.add('click', function(e) {
      const objectId = e.get('objectId');
      const object = objectManager.objects.getById(objectId);
      console.log('Clicked on:', object.properties.get('balloonContent'));
    });

    // Добавляем ObjectManager на карту
    map.geoObjects.add(objectManager);

    // Логирование изменения области видимости
    map.events.add('boundschange', function() {
      const bounds = map.getBounds();
      try {
        const distance = ymaps.coordSystem.geo.getDistance(
          [bounds[0][0], bounds[0][1]],
          [bounds[1][0], bounds[1][1]]
        );
        console.log(`Область: ${(distance/1000).toFixed(2)}км, Масштаб: ${map.getZoom()}`);
      } catch(e) {
        console.log("Ошибка: " + e.message);
      }
    });
  }
});