import { normalize, schema } from 'normalizr';
import api from '../../services/api';
import eventsSchema from '../schema/events'

export default ({commit, state}, id) => {
    return api.users.read(id).then(({data: {user, totals, events}}) => {
        commit('setUserProfile', user);
        commit('setTotals', totals);

        let normalized = normalize({id: 'main', events}, eventsSchema);

        let showedEvents = normalized.entities._.main.events;
        delete normalized.entities._;

        let mutations = Object.assign({}, normalized.entities, {
            setShowedEvents: showedEvents
        });

        Object.entries(mutations).forEach(
            ([mutation, payload]) => commit(mutation, payload)
        );

        return user;
    })
}