import 'vite/modulepreload-polyfill';
import './style.scss';

// Slider - Library import example
// import { tns } from "tiny-slider"

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
});