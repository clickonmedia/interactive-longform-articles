'use strict';

import React from 'react';

const SectionList = (props) => {

	const { items } = props;

	console.log( 'Section list', items );

	// const visibility = 'transparent';
	const visibility = '';

	return (
		<React.Fragment>
			{ items.map( ( item, index ) => (
				<div key={ index } className={`interactive-section ${ item.int_section_type } ${ item.color } ${ visibility }`}>
					<div dangerouslySetInnerHTML={{ __html: item.progress }} />
					<div dangerouslySetInnerHTML={{ __html: item.background }} />
					<div className="interactive-text">
						<div className="content" dangerouslySetInnerHTML={{ __html: item.content }}></div>
					</div>
				</div>
			)) }
		</React.Fragment>
	);
}

export default SectionList