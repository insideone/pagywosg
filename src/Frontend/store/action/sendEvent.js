import api from '../../services/api';

export default ({commit, state}, event) => {
    return api.events[event.id ? 'update' : 'create'](event).then(({data: result}) => {
        return result;
    });
}
