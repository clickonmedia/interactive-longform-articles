'use strict';

import React from 'react';
import PropTypes from 'prop-types';

const AppHeader = (props) => {

	const { brand, permalink, title } = props;

	return (
		<div className="interactive-header">

			{ /* Site branding */}
			<a href="/" className="brand">
				{ brand.logo &&
					<img src={ brand.logo } alt={ brand.name } />
				}
				{ !brand.logo &&
					<div className="title">{ brand.name }</div>
				}
			</a>

			<div className="social-share">
				<a className="link ia-icon twitter ia-icon-twitter"
					href={`https://twitter.com/intent/tweet?url=${ permalink }&text=${ title }`}
					target="_blank">
				</a>

				<a className="link ia-icon facebook ia-icon-facebook-official"
					href={`https://www.facebook.com/sharer/sharer.php?u=${ permalink }`}
					target="_blank" >
				</a>
			</div>
		</div>
	);
}

AppHeader.propTypes = {
	brand: PropTypes.shape({
		name: PropTypes.string.isRequired,
		logo: PropTypes.string
	}),
	permalink: PropTypes.string.isRequired,
	title: PropTypes.string.isRequired
}

export default AppHeader;