import api from '../../services/api';

export default ({commit}, entryVerify) => {
    return api.events.verify(entryVerify).then(({data: result}) => {
        if (result.success) {
            commit('setEventEntryFields', {
                id: entryVerify.id,
                fields: {[entryVerify.field]: entryVerify.value}}
            );
        }
        return result;
    });
}
