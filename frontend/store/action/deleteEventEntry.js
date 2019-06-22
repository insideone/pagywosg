import api from '../../services/api'

export default ({commit}, eventEntry) => {
    return api.events.deleteEntry(eventEntry).then(({data: result}) => {
        if (result.hasOwnProperty('success')) {
            commit('deleteEventEntry', eventEntry)
        }
        return result;
    });
};

