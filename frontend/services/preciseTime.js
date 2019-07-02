export default function (value) {
    let hours = parseInt(value / 60);
    let minutes = parseInt(value % 60);

    return `${hours}h ${minutes}m`;
}