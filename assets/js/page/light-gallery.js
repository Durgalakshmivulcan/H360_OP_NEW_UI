"use strict";
$(function () {
  if (typeof $.fn.lightGallery !== "function") return;
  $("#aniimated-thumbnials").lightGallery({
    thumbnail: true,
    selector: "a",
  });
});
