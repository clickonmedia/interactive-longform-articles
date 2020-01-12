'use strict';

import _ from 'lodash';

const SectionList = (props) => {

	const { items } = props;

	console.log( 'Section list items', items );

	const item = _.first( items );

	return (
		<div className={`interactive-section ${item.int_section_type} ${item.color} transparent`}>
			<div dangerouslySetInnerHTML={{ __html: item.progress }} />
			<div dangerouslySetInnerHTML={{ __html: item.background }} />
			<div className="interactive-text">
				<div className="content" dangerouslySetInnerHTML={{ __html: item.content }}></div>
			</div>
		</div>
	);
}

export default SectionList