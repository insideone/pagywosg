import { schema } from 'normalizr'

import userSchema from './user';
import gameCategorySchema from './gameCategory'
import eventEntrySchema from './eventEntry'

const eventSchema = new schema.Entity('addEvents', {
    gameCategories: [gameCategorySchema],
    host: userSchema,
    entries: [eventEntrySchema]
});

export default eventSchema;
