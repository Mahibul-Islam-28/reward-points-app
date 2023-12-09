import { StyleSheet } from 'react-native';

const styles = StyleSheet.create({
    body: {
        overflowX: 'hidden',
        fontSize: 20,
        fontFamily: 'Roboto Slab',
        margin: 0,
        padding: 0,
    },
    root: {
        backgroundColor: '#72b8d2',
    },


    container: {
        width: '90%',
        margin: 0,
        flex: 1,
        alignItems: "center",
        justifyContent: "center",
    },

    menuContainer: {
        backgroundColor: '#D3D3D3',
        width: '100%',
        flexDirection: 'row',
        alignItems: "center",
        justifyContent: "space-evenly",
        marginBottom: 30,
    },

    menuButton: {
        paddingHorizontal: 15,
        paddingVertical: 20,
        fontWeight: 'bold'
    },

    menuText: {
        fontWeight: 'bold'
    },

    table: {
        border: '1px solid #333',
    },
    tableRow: {
        paddingVertical: 20,
    },
    tableData: {
        borderWidth: 1,
        borderColor: "thistle",
        borderRadius: 50,
        fontSize: 25,
    },
    th: {
        border: '1px solid #333',
    },
    td:{
        padding: 5,
        backgroundColor: '#111',
        color: '#fff',
    },
    th: {
        padding: 5,
        backgroundColor: '#111',
        color: '#fff',
    },

    rewardCard: {
        textAlign: 'center',
        color: 'red',
        marginTop: '50px',
    },
    number: {
        borderColor: '#FE4E12',
        borderStyle: 'solid',
        borderWidth: 3,
        paddingVertical: 25,
        paddingHorizontal: 23,
        color: 'white',
        backgroundColor: '#111',
        fontWeight: 'bold',
        backgroundColor: '#333',
        fontSize: 20,
        width: 100,
        height: 100,
        textAlign: 'center',
        paddingVertical: 35,
        borderRadius: 50,
    },

    cardText: {
        marginTop: 20,
        textAlign: 'center',
        fontWeight: 'bold',
    },

    loginForm: {
        padding: 20,
        boxShadow: '1px 5px 15px #777',
        backgroundColor: '#e3fbff',
        width: 280,
    },

    input: {
        paddingHorizontal: 10,
        paddingVertical: 13,
        borderColor: '#333',
        borderStyle: 'solid',
        borderWidth: 1,
        marginVertical: 11,
    },
    hiddenInput: {
        display: 'none',
    },
    dropDown: {
        backgroundColor: '#333',
        width: '100%'
    },
    submitBtn: {
        paddingVertical: 20,
        marginVertical: 25,
        borderColor: '#333',
        borderStyle: 'solid',
        borderWidth: 3,
    },
    formTitle: {
        fontSize: 20,
        textAlign: 'center',
        marginBottom: 10,
        fontWeight: 'bold',
        textTransform: 'uppercase',
    },

    registerForm: {
        padding: 25,
        boxShadow: '1px 5px 15px #777',
        backgroundColor: '#cee4e7',
        width: '40%',
        minWidth: 280,
    },

    invoiceForm: {
        padding: 25,
        boxShadow: '1px 5px 15px #777',
        backgroundColor: '#cec4e7',
        width: '45%',
        minWidth: 280,
    },

    invoiceButton: {
        marginTop: 55,
        backgroundColor: '#333',
        paddingHorizontal: 20,
        paddingVertical: 15,
        borderRadius: 20,
        color: '#fff',
        fontWeight: 'bold'
    },
    logoutButton: {
        position: 'absolute',
        top: 5,
        right: -20,
        marginTop: 5,
        backgroundColor: '#999',
        paddingHorizontal: 20,
        paddingVertical: 15,
        borderRadius: 20,
        color: '#fff',
        fontWeight: 'bold'
    },
    datePickerStyle: {
        width: '100%',
        marginTop: 20,
      },
      
   
});

export{ styles }

