import React from 'react';
import { useBlockProps } from '@wordpress/block-editor';
import { RawHTML } from '@wordpress/element';

export interface IFormSaveBlockProps {
  attributes: { portalId: string; formId: string };
}

export default function FormSaveBlock({ attributes }: IFormSaveBlockProps) {
  const { portalId, formId } = attributes;

  if (portalId && formId) {
    return (
      <RawHTML {...useBlockProps.save()}>
        {`[hubspot portal="${portalId}" id="${formId}" type="form"]`}
      </RawHTML>
    );
  }
  return null;
}
