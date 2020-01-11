'use strict';

const e = React.createElement;

class App extends React.Component {

    constructor(props) {
        super(props);

        this.state = { enabled: true };
    }

    render() {

    	console.log('interactive_article_data', interactive_article_data);

        if ( ! this.state.enabled ) {
            return (<div />);
        }

        return (
            <div>Interactive Longform Articles</div>
        );
    }
}

const domContainer = document.querySelector(' #interactive-article-container' );
ReactDOM.render( e(App), domContainer );