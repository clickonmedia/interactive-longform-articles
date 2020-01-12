'use strict';

const e = React.createElement;

import SectionList from './SectionList.js';

class App extends React.Component {

    constructor(props) {
        super(props);

        this.state = { enabled: true };
    }

    render() {

    	const data = interactive_article_data;

    	console.log('interactive_article_data', interactive_article_data);

        if ( ! this.state.enabled ) {
            return (<div />);
        }

        return (
        	<div>
        		<div>Interactive Longform Articles</div>
        		<SectionList items={ data.sections } />
        	</div>
        );
    }
}

const domContainer = document.querySelector(' #interactive-article-container' );
ReactDOM.render( e(App), domContainer );