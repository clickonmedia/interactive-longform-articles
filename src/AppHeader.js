'use strict';

import React from 'react';
import PropTypes from 'prop-types';

const AppHeader = (props) => {
	return (
		<div className="interactive-header">
			<a href="/" className="brand">{ props.brand }</a>

			<div className="social-share">
				<a className="link ia-icon twitter ia-icon-twitter"
					href={`https://twitter.com/intent/tweet?url=${ props.permalink }&text=${ props.title }`}
					target="_blank">
				</a>

				<a className="link ia-icon facebook ia-icon-facebook-official"
					href={`https://www.facebook.com/sharer/sharer.php?u=${ props.permalink }`}
					target="_blank" >
				</a>
			</div>
		</div>
	);
}

AppHeader.propTypes = {
	brand: PropTypes.string,
	permalink: PropTypes.string.isRequired,
	title: PropTypes.string.isRequired
}

export default AppHeader;