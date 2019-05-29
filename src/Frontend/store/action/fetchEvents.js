import { normalize, schema } from 'normalizr';
import api from '../../services/api';
import eventsSchema from '../schema/events'

export default ({commit, state}, filter) => {
    return api.events.getList(filter).then(({data: {events}}) => {

        let normalized = normalize({id: 'main', events}, eventsSchema);

        let showedEvents = normalized.entities._.main.events;
        delete normalized.entities._;

        let mutations = Object.assign({}, normalized.entities, {
            setShowedEvents: showedEvents
        });

        Object.entries(mutations).forEach(
            ([mutation, payload]) => commit(mutation, payload)
        );

        return events;
    });
}
