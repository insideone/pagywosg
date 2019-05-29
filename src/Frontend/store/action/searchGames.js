import api from '../../services/api';

export default ({commit}, {query, page}) => {
    return api.games.search(query, page).then(({data: {games, maxPageNumber}}) => {
        return {games: games, maxPageNumber: maxPageNumber};
    })
}
