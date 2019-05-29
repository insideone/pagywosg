import api from '../../services/api';

export default ({commit, state}) => {
    return api.playStatuses.getList().then(({data: {statuses}}) => {
        let playStatuses = {};

        statuses.forEach(
            playStatus => playStatuses[playStatus.id] = playStatus
        );

        commit('addPlayStatuses', playStatuses);
        return statuses;
    });
}
