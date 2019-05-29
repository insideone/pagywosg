import api from '../../services/api'
import { normalize } from 'normalizr';
import eventEntrySchema from '../schema/eventEntry'

export default ({commit}, eventEntry) => {
    let method = eventEntry.hasOwnProperty('id')
        ? 'updateEntry'
        : 'createEntry';

    return api.events[method](eventEntry).then(({data: result}) => {
        let entry = result.entry;

        if (!entry)
            return result;

        let normalized = normalize(entry, eventEntrySchema);

        const mutations = Object.assign({}, normalized.entities, {
            addEntryToEvent: {eventId: eventEntry.eventId, entryId: entry.id}
        });

        normalized.entities.addEventEntries[entry.id].event = eventEntry.eventId;

        Object.entries(mutations).forEach(
            ([mutation, payload]) => commit(mutation, payload)
        );

        return result;
    });
};

