import React from 'react';

import { SelectControl } from '@wordpress/components';
import withMetaData from '../../utils/withMetaData';
import { monitorSidebarMetaChange } from '../../api/hubspotPluginApi';

interface IUISidebarSelectControlProps {
  metaValue?: string;
  metaKey: string;
  setMetaValue?: Function;
  options: any[];
  className: string;
  label: any;
}

const UISidebarSelectControl = (props: IUISidebarSelectControlProps) => {
  return (
    <SelectControl
      value={props.metaValue}
      onChange={content => {
        if (props.setMetaValue) {
          props.setMetaValue(content);
        }
        monitorSidebarMetaChange(props.metaKey);
      }}
      {...props}
    />
  );
};

export default withMetaData(UISidebarSelectControl);
