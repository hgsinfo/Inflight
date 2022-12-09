import ReactDOM from 'react-dom';
import MeetingControlController from './MeetingControlController';
import MeetingWidgetController from './MeetingWidgetController';

export default class registerMeetingsWidget {
  widgetContainer: any;
  controlContainer: any;
  setValue: Function;
  attributes: any;

  constructor(controlContainer: any, widgetContainer: any, setValue: Function) {
    const attributes = widgetContainer.dataset.attributes
      ? JSON.parse(widgetContainer.dataset.attributes)
      : {};

    this.widgetContainer = widgetContainer;
    this.controlContainer = controlContainer;
    this.setValue = setValue;
    this.attributes = attributes;
  }

  render() {
    ReactDOM.render(
      MeetingWidgetController(this.attributes, this.setValue)(),
      this.widgetContainer
    );

    ReactDOM.render(
      MeetingControlController(this.attributes, this.setValue)(),
      this.controlContainer
    );
  }

  done() {
    ReactDOM.unmountComponentAtNode(this.widgetContainer);
    ReactDOM.unmountComponentAtNode(this.controlContainer);
  }
}
