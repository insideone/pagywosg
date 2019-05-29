import { normalize, schema } from 'normalizr';
import api from '../../services/api';
import eventsSchema from '../schema/events';
import userSchema from "../schema/user";

export default ({commit, state}, eventId) => {
    return api.events.getLeaderboard(eventId).then(({data: data}) => {
        let leaderboard = data.leaderboard;

        const leaderboardItemSchema = new schema.Entity('_', {
            player: userSchema
        });

        const leaderboardSchema = new schema.Entity('_', {
            entries: [leaderboardItemSchema]
        });

        let normalizedLeaderboard = normalize({id: 'main', entries: leaderboard.entries}, leaderboardSchema);

        let mutations = Object.assign({}, normalizedLeaderboard.entities, {
            setLeaderboard: leaderboard.entries.map(entry => {
                entry.player = entry.player.id;
                return entry;
            })
        });
        delete mutations._;

        Object.entries(mutations).forEach(
            ([mutation, payload]) => commit(mutation, payload)
        );

        let normalizedEvents = normalize({id: 'main', events: leaderboard.events}, eventsSchema);

        delete normalizedEvents.entities._;

        Object.entries(normalizedEvents.entities).forEach(
            ([mutation, payload]) => commit(mutation, payload)
        );

        return data;
    })

}
