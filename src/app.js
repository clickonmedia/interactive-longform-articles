'use strict';

const e = React.createElement;

class App extends React.Component {

    constructor(props) {
        super(props);

        this.state = { enabled: true };
    }

    render() {

        if ( ! this.state.enabled ) {
            return (<div />);
        }

        return (
            <div>React app</div>
        );
    }
}

const domContainer = document.querySelector(' #interactive-article-container' );
ReactDOM.render( e(App), domContainer );