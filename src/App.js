'use strict';

import React from 'react';
import ReactDOM from 'react-dom';
import _ from 'lodash';

import AppHeader from './components/AppHeader';
import SectionList from './components/SectionList';

const containerStyles = {
	position: 'absolute',
	top: 0,
	width: '100vw',
	height: '300vh',
	overflowX: 'scroll'
}

class App extends React.Component {

    constructor(props) {
        super(props);

        this.state = {
        	brand: {
        		name: ''
        	},
        	sections: [],
			shareURL: '',
			shareTitle: '',
			elements: [],
        	currentIndex: 0
        };

        this.handleScroll = this.handleScroll.bind(this);
        this.checkVisibility = this.checkVisibility.bind(this);
    }

    componentDidMount() {
    	console.log('cmd scroll');

    	const shareURL = window.location.href;
    	const shareTitle = document.title;

		const elements = document.querySelectorAll('.interactive-section');
		console.log( 'elements', elements );
		
		this.setState({
			shareURL,
			shareTitle,
			elements
		});
    }

	checkVisibility() {
		
		let { currentIndex, elements } = this.state;
		console.log( 'element', currentIndex );

		const currentElement = elements[currentIndex];

		const rect = currentElement.getBoundingClientRect();
		console.log( 'bottom', rect.bottom );

		if ( rect.bottom < window.innerHeight / 2 ) {
			console.log('change');

			this.setState( ( prevState ) => {
				return {
					currentIndex: prevState.currentIndex + 1	
				}
			});
		}
	}

    handleScroll( e ) {
    	console.log('scroll');
    }

    render() {
    	const data = interactive_article_data;

    	const { brand, sections } = data;
    	const { shareTitle, shareURL, currentIndex } = this.state;

    	console.log( 'sections', sections );
    	console.log( 'brand', brand );

        return (
        	<div className="inner-container" style={ containerStyles } onScroll={ _.throttle( this.checkVisibility, 50 ) }>
        		<AppHeader 
        			brand={ brand }
        			permalink={ shareURL }
        			title={ shareTitle } />
        		<SectionList items={ sections } currentIndex={ currentIndex } />
        	</div>
        );
    }
}

const domContainer = document.querySelector('.interactive-article-container' );
ReactDOM.render( React.createElement(App), domContainer );