'use strict';

const e = React.createElement;

import AppHeader from './AppHeader';
import SectionList from './SectionList';

class App extends React.Component {

    constructor(props) {
        super(props);

        this.state = { enabled: true };
    }

    render() {

    	const data = interactive_article_data;

    	const shareURL = window.location.href;
    	const shareTitle = document.title;

    	console.log('interactive_article_data', interactive_article_data);

        if ( ! this.state.enabled ) {
            return (<div />);
        }

        return (
        	<div>
        		<AppHeader 
        			brand="Brand name"
        			permalink={ shareURL }
        			title={ shareTitle } />
        		<SectionList items={ data.sections } />
        	</div>
        );
    }
}

const domContainer = document.querySelector('.interactive-article-container' );
ReactDOM.render( e(App), domContainer );