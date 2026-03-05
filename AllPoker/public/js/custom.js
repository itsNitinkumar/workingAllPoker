$(document).ready(function () {

  $("#menu_bar").click(function () {
    $(".leftMenu").toggleClass("side-nav");
  });

  $(".mobile-menu").click(function () {
    $(".bg-blur").removeClass("nav-show");
  });

  $("#menu-btn").click(function () {
    $(".bg-blur").toggleClass("nav-show");
  });

  /* ================= Emoji Toggle (ORIGINAL SYSTEM – DO NOT TOUCH) ================= */

  $(".smily, .buy-cross4, .hurt").click(function () {
    $(".emoji-main").slideToggle();
  });

  $(".smily, .buy-cross4, .hurt").click(function () {
    $(".emoji-main-inner").toggleClass("add-after d-block");
  });

  /* ================================================================================= */

  var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
  var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
    return new bootstrap.Tooltip(tooltipTriggerEl);
  });

  $("#filter-icon").click(function () {
    $(".check-main-inner").slideToggle();
    $(this).toggleClass("fill-bg");
  });

  $("#filter-icon2").click(function () {
    $(".check-main-inner").slideToggle();
    $(this).toggleClass("fill-bg");
  });

  $(".item-main").click(function () {
    $(".filter-list ul li a").removeClass("test");
    $(this).toggleClass("test");
    var index = $(this).parent().index();
    $(".filter-list ul li:eq(" + index + ") a").toggleClass("test");
  });

  $(".drop-item2").click(function () {
    $(".filter2 ul li a").removeClass("test");
    $(this).toggleClass("test");
    var index = $(this).parent().index();
    $(".filter2 ul li:eq(" + index + ") a").toggleClass("test");
  });

  //-----JS for Price Range slider-----
  $(function () {
    $("#price-range").slider({
      range: "min",
      min: 0,
      max: 1000,
      value: 500,
      slide: function (event, ui) {
        $("#min-price").text("$" + ui.value);
      }
    });
    $("#min-price").text("$" + $("#price-range").slider("value"));
  });

  $(function () {
    $("#price-range2").slider({
      range: "min",
      min: 0,
      max: 7550,
      value: 2550,
      slide: function (event, ui) {
        $("#min-price2").text("$" + ui.value);
      }
    });
    $("#min-price2").text("$" + $("#price-range2").slider("value"));
  });

  $(function () {
    $("#price-range3").slider({
      range: "min",
      min: 0,
      max: 2000,
      value: 550,
      slide: function (event, ui) {
        $("#min-price3").text("$" + ui.value);
      }
    });
    $("#min-price3").text("$" + $("#price-range3").slider("value"));
  });

  $(function () {
    $("#price-range4").slider({
      range: "min",
      min: 0,
      max: 2000,
      value: 550,
      slide: function (event, ui) {
        $("#min-price4").text("$" + ui.value);
      }
    });
    $("#min-price4").text("$" + $("#price-range4").slider("value"));
  });

  $(function () {
    $("#price-range5").slider({
      range: "min",
      min: 0,
      max: 2000,
      value: 550,
      slide: function (event, ui) {
        $("#min-price5").text("$" + ui.value);
      }
    });
    $("#min-price5").text("$" + $("#price-range5").slider("value"));
  });

  $("#cross").click(function () {
    $(".hand-rank").removeClass("box-show");
  });

  $(".gif-show li a").click(function () {
    $(".puke").toggleClass("puke-show");
  });

  $(".emojis-list li a").click(function () {
    $(".react-img").toggleClass("d-block");
    $(".react-emoji-main").removeClass("d-block");
  });

  $(".quesion").click(function () {
    $(".hand-main-inner").addClass("add-before");
    $(".buy-hero").addClass("z-ind2");
    $(".buy-cross6").addClass("cross-show");
    $(".hand-rank-mbl").toggleClass("box-show");
    $(".fold-main").addClass("fold-main-hide");
    $(".cards-main-inner").addClass("fold-main-hide");
  });

  $(".buy-cross6").click(function () {
    $(".hand-main-inner").addClass("remove-before2");
    $(".camera-setting-main2").addClass("test");
    $(".buy-cross6").addClass("cross-hide");
    $(".header-2").addClass("z-ind3");
  });

  $(".switch-red").click(function () {
    $(this).toggleClass("switch-red-btn");
  });

  $(".switch-sky").click(function () {
    $(this).toggleClass("switch-sky-btn");
  });

  $(".react-loby").click(function () {
    $(".react-emoji-main ").toggleClass("d-block");
  });

  /* ================= POPUPS (UNCHANGED) ================= */

  $("#buyInBtn, .fund-cross").click(function () {
    $(".enter-buy-main").toggleClass("d-block");
  });

  $(".fold-btn, .buy-cross3").click(function () {
    $(".camera-setting-main2").toggleClass("d-block");
  });

  $(".fold-btn, .buy-cross3, #buyInBtn, .fund-cross, #timeoutBtn, .time-cross, .leave-cross, #exitBtn").click(function () {
    $(".buy-hero").toggleClass("z-up");
  });

  $(".raise-btn, .deposit-cross").click(function () {
    $(".enter-buy-main2").toggleClass("d-block");
  });

  $(".buy-cross7, .quesion").click(function () {
    $(".hand-rank-mbl").toggleClass("box-show1");
  });
  $(".buy-cross7, .quesion").click(function () {
    $(".hand-rank-mbl").toggleClass("d-flex");
  });

  $("#cross, .quesion2").click(function () {
    $(".hand-rank-desktop").toggleClass("d-block");
  });

  $(".leave-table, .leave-cross, #exitBtn").click(function () {
    $(".time-main").toggleClass("d-block");
    $(".time-main-2").removeClass("d-block");
  });

  $("#timeoutBtn, .time-cross").click(function () {
    $(".time-main-2").toggleClass("d-block");
  });

  $(".video-icon").click(function () {
    $(".react-video").toggleClass("d-block");
    $(".react-video").toggleClass("opacity-100");
    $(".loby-avatar").toggleClass("d-none");
  });

  /* ================= Icon active styles (FIXED) ================= */

  // Prevent chat icon from toggling active when Proof/Support open via programmatic smily click
  let __suppressSmilyActive = false;

  // Prevent the smily handler from marking chat active when it was triggered by Proof/Support
  let __smilyTriggeredByOther = false;

  function setEmojiActive(source) {
    // source: "chat" | "proof" | "support" | null
    $(".smily, .support, #proofBtn").removeClass("icon-bg");
    if (source === "chat") $(".smily").addClass("icon-bg");
    if (source === "support") $("a.support").addClass("icon-bg");
    if (source === "proof") $("#proofBtn").addClass("icon-bg");
  }

  $(".message, .video-icon, .support, .mute-sound, .smily").click(function () {
    if (__suppressSmilyActive && $(this).is(".smily")) return;
    $(this).toggleClass("icon-bg");
  });

  $('.emojis-list li a').on('click', function () {
    $("react-img").toggleClass('playing').delay(3000);
  });

  $('.emojis-list li a').on('click', function () {
    $(function () {
      setTimeout(function () { $(".react-img").toggleClass('d-block').delay(3000); }, 5000);
    });
  });

  $(function () {
    setTimeout(function () { $(".puke").toggleClass('playing2').delay(9000); }, 9000);
  });

  $(".hurt").click(function () {
    $(".my-react-img").toggleClass("show");
  });

  /* =====================================================================
     Emoji helpers
     ===================================================================== */

  function isEmojiOpenNow() {
    return $(".emoji-main-inner").hasClass("add-after") || $(".emoji-main:visible").length > 0;
  }

  function showTabById(tabId) {
    const btn = document.getElementById(tabId);
    if (btn && window.bootstrap?.Tab) {
      bootstrap.Tab.getOrCreateInstance(btn).show();
    }
  }

  function closeEmojiEverywhere() {
    $(".emoji-main-inner").removeClass("add-after d-block");
    $(".emoji-main").stop(true, true).slideUp(150);
    setEmojiActive(null); // NOTHING active after close
  }

  /* =====================================================================
     Proof + Support: toggle open/close like chat, but keep correct active icon
     ===================================================================== */

  // Proof
  $(document).on("click", "#proofBtn", function (e) {
    e.preventDefault();

    var wasOpen = isEmojiOpenNow();

    __smilyTriggeredByOther = true;
    __suppressSmilyActive = true;
    $(".smily").trigger("click");
    __suppressSmilyActive = false;

    if (!wasOpen) {
      setTimeout(function () {
        showTabById("info-tab");
        setEmojiActive("proof");
        __smilyTriggeredByOther = false;
      }, 0);
    } else {
      setTimeout(function () {
        if (!isEmojiOpenNow()) setEmojiActive(null);
        __smilyTriggeredByOther = false;
      }, 200);
    }
  });

  // Support
  $(document).on("click", "a.support", function (e) {
    e.preventDefault();

    var wasOpen = isEmojiOpenNow();

    __smilyTriggeredByOther = true;
    __suppressSmilyActive = true;
    $(".smily").trigger("click");
    __suppressSmilyActive = false;

    if (!wasOpen) {
      setTimeout(function () {
        showTabById("contact-tab");
        setEmojiActive("support");
        __smilyTriggeredByOther = false;
      }, 0);
    } else {
      setTimeout(function () {
        if (!isEmojiOpenNow()) setEmojiActive(null);
        __smilyTriggeredByOther = false;
      }, 200);
    }
  });

  // Chat icon:
  // - If user clicked chat, set chat active when open, clear when closed.
  // - If chat click was triggered by proof/support, do NOT set chat active.
  $(document).on("click", ".smily", function () {
    setTimeout(function () {
      if (__smilyTriggeredByOther) return;

      if (isEmojiOpenNow()) {
        showTabById("home-tab");
        setEmojiActive("chat");
      } else {
        setEmojiActive(null);
      }
    }, 0);
  });

  /* =====================================================================
     Close behavior:
       - click outside closes
       - closing any other way clears actives
       - NO tab snap before closing (we do not change tab on close)
     ===================================================================== */

  // Click outside closes emoji
  $(document).on("click", function (e) {
    if (!isEmojiOpenNow()) return;

    var inside = $(e.target).closest(
      ".emoji-main, .emoji-main-inner, .smily, #proofBtn, a.support, .buy-cross4"
    ).length > 0;

    if (!inside) closeEmojiEverywhere();
  });

  // X close clears actives
  $(document).on("click", ".buy-cross4", function () {
    setTimeout(function () {
      if (!isEmojiOpenNow()) setEmojiActive(null);
    }, 0);
  });

  // If closed by hurt toggle, clear actives after animation
  $(document).on("click", ".hurt", function () {
    setTimeout(function () {
      if (!isEmojiOpenNow()) setEmojiActive(null);
    }, 200);
  });

});

/* ==========================================================
   EMOJI PANEL HARD RESET (SAFETY NET)
   Forces ALL icons inactive whenever emoji panel is CLOSED
   ========================================================== */
(function () {

  function emojiIsOpen() {
    var innerOpen = document.querySelector(".emoji-main-inner")?.classList.contains("add-after");
    var panel = document.querySelector(".emoji-main");
    var visible = panel && (panel.offsetParent !== null) && getComputedStyle(panel).display !== "none";
    return !!(innerOpen || visible);
  }

  function clearAllEmojiActives() {
    // Clear only emoji-related actives (doesn't touch video/mute/etc)
    document.querySelectorAll(".smily, a.support, #proofBtn").forEach(function (el) {
      el.classList.remove("icon-bg");
    });
  }

  function enforce() {
    if (!emojiIsOpen()) clearAllEmojiActives();
  }

  // Run after any likely close/open click (bubble phase, after your handlers)
  document.addEventListener("click", function (e) {
    var t = e.target;
    if (
      t.closest(".smily") ||
      t.closest("#proofBtn") ||
      t.closest("a.support") ||
      t.closest(".buy-cross4") ||
      t.closest(".hurt") ||
      t.closest(".emoji-main") ||
      t.closest(".emoji-main-inner")
    ) {
      // After animations/toggles settle, enforce
      setTimeout(enforce, 250);
      setTimeout(enforce, 600);
    } else {
      // Outside click could close it too
      setTimeout(enforce, 250);
    }
  }, false);

  // Also enforce on escape key (if you later add ESC close)
  document.addEventListener("keydown", function (e) {
    if (e.key === "Escape") {
      setTimeout(enforce, 50);
      setTimeout(enforce, 250);
    }
  });

  // Initial enforce on load
  setTimeout(enforce, 50);

})();
