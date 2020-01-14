'use strict';

import React from 'react';
import ReactDOM from 'react-dom';

import AppHeader from './AppHeader';
import SectionList from './SectionList';


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

        this.state = { enabled: true };

        this.handleScroll = this.handleScroll.bind(this);
    }

    componentDidMount() {
    	console.log('cmd scroll');
    }

    handleScroll( e ) {
    	console.log('scroll', e);
    }

    render() {

    	const data = interactive_article_data;

    	const shareURL = window.location.href;
    	const shareTitle = document.title;

    	console.log('interactive_article_data', interactive_article_data);

        if ( ! this.state.enabled ) {
            return (<div />);
        }

        const { brand, sections } = data;

        return (
        	<div className="inner-container" style={ containerStyles } onScroll={ this.handleScroll }>
        		<AppHeader 
        			brand={ brand }
        			permalink={ shareURL }
        			title={ shareTitle } />
        		<SectionList items={ sections } />
        	</div>
        );
    }
}

const domContainer = document.querySelector('.interactive-article-container' );
ReactDOM.render( React.createElement(App), domContainer );