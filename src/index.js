import 'vite/modulepreload-polyfill';
import './style.scss';

// Slider - Library import example
import { tns } from "tiny-slider"
import { gsap } from "gsap";
import { DrawSVGPlugin } from "gsap/DrawSVGPlugin";
import { ScrollTrigger } from "gsap/ScrollTrigger";
gsap.registerPlugin(DrawSVGPlugin, ScrollTrigger);

document.addEventListener('DOMContentLoaded', () => {
	// Prevent nav animations running when page first loaded
	setTimeout( () => {
		document.body.classList.add( "is-loaded" );
	}, 500 );

	// Lazy load fade in
	document.querySelectorAll( 'img[loading="lazy"]' ).forEach( ( img ) => {
		if( img.complete === true ) {
			img.classList.add( 'has-loaded' );
		}
		img.addEventListener( "load", ( e ) => {
			e.target.classList.add( 'has-loaded' );
		} );
	} );

	// Copyright Year
	document.querySelectorAll(".copyright").forEach( ( p ) => { 
		p.innerHTML = p.innerHTML.replace( '{YEAR}', new Date().getUTCFullYear() );
	} );

	// Nav Trigger 
	document.querySelectorAll( ".navigation-trigger a" ).forEach( link => { 
		link.addEventListener( "click", e => {
			e.preventDefault();
			document.body.classList.toggle( "nav-open" );
		} );
	} );

	// Slider gallery
	document.querySelectorAll( ".wp-block-gallery.is-style-slider-gallery" ).forEach( gallery => {
		const slider = tns( {
			container: gallery,
			autoWidth: true,
			loop: true,
			autoplay: true,
			gutter: 12,
			controls: false,
			navPosition: "bottom",
			autoplay: true,
			autoplayButton: false,
			autoplayTimeout: 4000,
			speed: 500,
			// mouseDrag: true
		} );

		let info = slider.getInfo();
		const counter = document.createElement( "div" );
		counter.classList.add( "counter" );
		counter.innerHTML = `${info.displayIndex} / ${info.slideCount}`;

		const ref = gallery.parentElement.appendChild( counter );

		slider.events.on( 'transitionEnd', () => {
			info = slider.getInfo();
			ref.innerHTML = `${info.displayIndex} / ${info.slideCount}`;
		} );
	} );

	document.querySelectorAll( ".wp-block-gallery.is-style-slider-gallery-lg" ).forEach( gallery => {
		const slider = tns( {
			container: gallery,
			items: 1,
			mode: 'carousel',
			// autoWidth: true,
			loop: true,
			autoplay: true,
			gutter: 0,
			controls: false,
			nav: false,
			navPosition: "bottom",
			speed: 500
			// autoplayButton: true,
			// mouseDrag: true
		} );

		let info = slider.getInfo();
		const counter = document.createElement( "div" );
		counter.classList.add( "counter" );
		counter.innerHTML = `${info.displayIndex} / ${info.slideCount}`;

		const ref = gallery.parentElement.appendChild( counter );

		slider.events.on( 'transitionEnd', () => {
			info = slider.getInfo();
			console.log( info );
			ref.innerHTML = `${info.displayIndex} / ${info.slideCount}`;
		} );
	} );

	// Anchor links
	document.querySelectorAll( "a[href*='#']" ).forEach( link => {
		link.addEventListener( "click", e => {
			const anchorRegex = /#(.*)$/;
			const id = e.target.href.match( anchorRegex );
			if( id.length > 0 && document.querySelector( id[0] ) ) {
				e.preventDefault();
				document.querySelector( id[0] ).scrollIntoView( { behavior: "smooth" } );
				document.body.classList.toggle( "nav-open", false );
			}
		} );
	} );

	// Esc close nav
	document.addEventListener('keydown', (e) => {
		if (e.key === 'Escape') {
			document.body.classList.toggle( "nav-open", false );
		}
	});

	document.querySelectorAll( ".wp-block-buttons.expanded-read-more" ).forEach( button => {
		button.addEventListener( "click", e => {
			button.remove();
		} );
	} );

	// Centre line
	const initRedLine = () => {
		ScrollTrigger.getAll().forEach( st => st.kill() );
		const top = document.querySelector( ".center-line" ).offsetTop ?? '140';

		document.querySelectorAll( "#red-line" ).forEach( svg => {
			const water = svg.querySelector( ".red-lines-line" );
			gsap.set(water, {visibility:"visible"});
			gsap.to(water, {drawSVG:"0%", duration: 0, ease:"power2.in"});

			const tl = gsap.timeline({
				scrollTrigger: {
					scrub: 1,
					trigger: svg,
					start: `top ${top}px`,
					// markers: true,
					endTrigger: svg,
					end: 'bottom bottom',
				},
			});

			tl.to(water, {
				drawSVG:"100%",
				ease: "linear"
			});
		} );
	};

	if( document.querySelector( ".center-line" ) ) {
		initRedLine();
	}

	let resizeTimer;
	window.addEventListener( 'resize', () => {
		clearTimeout( resizeTimer );
		resizeTimer = setTimeout( initRedLine, 250 );
	} );
});