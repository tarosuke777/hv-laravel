import "./bootstrap";
import Alpine from "alpinejs";
import infiniteScroll from "./infinite-scroll";
import Swiper from "swiper/bundle";
import "swiper/css/bundle";

window.Swiper = Swiper;

window.Alpine = Alpine;
Alpine.data("infiniteScroll", infiniteScroll);
Alpine.start();
