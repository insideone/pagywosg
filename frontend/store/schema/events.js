import { schema } from 'normalizr'

import eventSchema from "./event";

const eventsSchema = new schema.Entity('_', {
    events: [eventSchema]
});

export default eventsSchema;
