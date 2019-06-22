import { normalize, schema } from 'normalizr';
import api from '../../services/api';
import eventSchema from '../schema/event'

export default ({commit, state}, id) => {
    return api.events.read(id).then(({data: {event, permissions}}) => {
        let normalized = normalize(event, eventSchema);

        let mutations = Object.assign({}, normalized.entities, {
            setShowedEvents: [Object.keys(normalized.entities.addEvents)[0]]
        });

        Object.entries(mutations).forEach(
            ([mutation, payload]) => commit(mutation, payload)
        );

        commit('setPermissions', permissions);

        return event;
    });
}
