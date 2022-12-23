activateTabsLeft();
activateTabsMobile();
activateTabsRight();
activateTabsScreenResizeHandler();

// Wird ausgeführt, wenn der Shop geladen ist (bzw. wenn Varianten geladen wurden.
$.subscribe('plugin/swAjaxVariant/onRequestData', function() {
    activateTabsLeft();
		activateTabsMobile();
		activateTabsRight();
		activateTabsScreenResizeHandler();
});

// Linke Tabs
function activateTabsLeft() {
		// Initiale Tabs aktivieren
		$('[data-activate-tab]').eq(0).addClass('active');
    $('[data-active-tab]').eq(0).addClass('active');
    
		// Action-Handler installiern
		// (Code wird ausgeführt, wenn Tab angeklickt wird)
		$('[data-activate-tab]').on('click', function () {
        // Id auslesen
				var id = $(this).data('activate-tab');
        
				// Aktuell aktives Tab markieren
				$('[data-activate-tab]').removeClass('active');
				$('[data-activate-tab-mobile]').removeClass('active');		// Mobiles Tab auch deaktivieren
        $(this).addClass('active');
        
				// Angeklicktes Tab einblenden, das andere ausblenden
				$('[data-active-tab]').removeClass('active');
				
				//TODO: Hier rechtes Tab nur deaktivieren, wenn wir in Mobile-Ansicht sind.
				
        $('[data-active-tab="' + id + '"]').addClass('active');
				
				// Hilfsklasse entfernen, die den rechten Container kennzeichnet.
				$('.custom-tabs-right').removeClass('mv-mobile-tab-activated');
    });
}

// Mobile Tab-Buttons, die nur in der mobilen Ansicht sichtbar sind,
// und in der Desktop Ansicht durch die auf der rechten Seite ersetzt werden.
function activateTabsMobile() {
		// Action-Handler installiern
		// (Code wird ausgeführt, wenn Tab angeklickt wird)
		$('[data-activate-tab-mobile]').on('click', function () {
				// Id auslesen
				var id = $(this).data('activate-tab-mobile');
        
				// Nicht länger aktive Tabs deaktivieren
				$('[data-activate-tab]').removeClass('active');
				$('[data-activate-tab-mobile]').removeClass('active');
				$('[data-activate-tab-right]').removeClass('active');
        
				// Aktuell aktives Tab markieren
				$(this).addClass('active');
				$('[data-activate-tab-right="' + id + '"]').addClass('active');
				        
				// Angeklicktes Tab einblenden, das andere ausblenden
				$('[data-active-tab]').removeClass('active');
				$('[data-active-tab-right]').removeClass('active');
        $('[data-active-tab-right="' + id + '"]').addClass('active');
				
				// Hilfsklasse zum rechten Container hinzufügen, um zu kennzeichnen, dass diese Seite jetzt angezeigt wird.
				$('.custom-tabs-right').addClass('mv-mobile-tab-activated');
    });
}

// Rechte Tabs
function activateTabsRight() {
    // Initiale Tabs aktivieren
		$('[data-activate-tab-right]').eq(0).addClass('active');
    $('[data-active-tab-right]').eq(0).addClass('active');
    
		// Action-Handler installiern
		// (Code wird ausgeführt, wenn Tab angeklickt wird)
		$('[data-activate-tab-right]').on('click', function () {
        // Id auslesen
				var id = $(this).data('activate-tab-right');
        
				// Aktuell aktives Tab markieren
				$('[data-activate-tab-right]').removeClass('active');
        $(this).addClass('active');
        
				// Angeklicktes Tab einblenden, das andere ausblenden
				$('[data-active-tab-right]').removeClass('active');
        $('[data-active-tab-right="' + id + '"]').addClass('active');
    });
}

// Handler initialisieren der auf eine veränderung der Bildschirmbreite lauscht.
// Wenn das mobile Tab aktiviert ist, wird das linke Tab auf das "Default-Tab" zurückgesetzt.
function activateTabsScreenResizeHandler() {
		//Auf Größenänderung müssen wir hier auch noch reagieren.
		//Wenn einer der mobilen Tabs von rechts aktiv ist während das Fenster klein ist,
		//resetten wir ab der Tablet Größe den linken Bereich auf das erste Tab - weil sonst der linke Bereich leer dargestellt wird!
		$(window).on('resize', function(){
				var win = $(this); //this = window
				
				if (win.width() > 767) {
						// Nur wenn mobiles Tab aktiviert ist.
						if($('.custom-tabs-right').hasClass('mv-mobile-tab-activated')) {
							
								// Mobiles Tab deaktivieren
								$('.custom-tabs-right').removeClass('mv-mobile-tab-activated');
								
								// Erstes Tab im linken Container aktivieren.
								$('.custom-tabs--btn').each(function(index, item) {
										if($(item).attr('data-activate-tab') == 1) {
												$(item).click();
										}
								});
						}
				}
		});
}