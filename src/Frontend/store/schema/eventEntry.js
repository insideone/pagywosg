import { schema } from 'normalizr'
import userSchema from './user'
import gameSchema from './game'
import gameCategorySchema from './gameCategory'
import playStatusSchema from './playStatus'
import eventSchema from './event'

const eventEntrySchema = new schema.Entity('addEventEntries', {
    game: gameSchema,
    player: userSchema,
    category: gameCategorySchema,
    playStatus: playStatusSchema
});

export default eventEntrySchema
