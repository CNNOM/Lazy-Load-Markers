const MapPoint = {
  map: null,
  mapElementId: 'map',
  center: [55.751574, 37.573856],
  defaultZoom: 12,
  pointZoom: 18,
  minZoom: 11,

  objectManager: null,

  config: {
    controls: ['zoomControl'],

    objectManager: {
      clusterize: true,
      gridSize: 100,
      clusterDisableClickZoom: true,
    },

    clusters: {
      preset: 'islands#invertedNightClusterIcons',
    },

    objects: {
      preset: 'islands#nightDotIcon',
    }
  },

  init() {
    ymaps.ready(() => this.initMap());
  },

  initMap() {
    this.createMap(); // создание  карты
    this.createObjectManager(); // настройка карты

    this.setupBoundsChangeHandler(); // изменение границ

    this.addObjectManagerToMap(); // добавление objectManager на карту

  },

  createMap() {
    this.map = new ymaps.Map(this.mapElementId, {
      center: this.center,
      zoom: this.defaultZoom,
      controls: this.config.controls
    });
  },

  createObjectManager() {
    this.objectManager = new ymaps.LoadingObjectManager(
      '/local/components/dterra/points/objects.php?bbox=%b',
      this.config.objectManager
    );

    this.setPointsObjectManager();
  },

  setPointsObjectManager() {
    this.objectManager.clusters.options.set(this.config.clusters);
    this.objectManager.objects.options.set(this.config.objects);
  },

  setupBoundsChangeHandler() {
    this.map.events.add('boundschange', () => this.handleBoundsChange());
  },

  handleBoundsChange() {
    const bounds = this.map.getBounds();
    const distance = ymaps.coordSystem.geo.getDistance(
      [bounds[0][0], bounds[0][1]],
      [bounds[1][0], bounds[1][1]]
    );
  },

  addObjectManagerToMap() {
    this.map.geoObjects.add(this.objectManager);
  },
};

document.addEventListener("DOMContentLoaded", function () {
  MapPoint.init();
});