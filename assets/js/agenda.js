// assets/js/agenda.js

document.addEventListener('DOMContentLoaded', () => {
    const agendaVisual = document.getElementById('agenda-visual');
    const prevWeekBtn = document.getElementById('prev-week');
    const nextWeekBtn =
        document.getElementById('next-week');
    const currentWeekDisplay = document.getElementById('current-week-display');

    let currentDate = new Date();

    const renderCalendar = (date) => {
        agendaVisual.innerHTML = ''; // Limpa a agenda antes de renderizar
        const weekStart = getWeekStart(date);
        
        updateWeekDisplay(weekStart);
        renderHeader(weekStart);
        renderTimeSlots();
        fetchAppointments(weekStart);
    };

    const getWeekStart = (date) => {
        const d = new Date(date);
        const day = d.getDay();
        // Considera Domingo (0) como início da semana
        const diff = d.getDate() - day; 
        return new Date(d.setDate(diff));
    };

    const updateWeekDisplay = (weekStart) => {
        const weekEnd = new Date(weekStart);
        weekEnd.setDate(weekStart.getDate() + 6);
        const options = { month: 'long', day: 'numeric' };
        currentWeekDisplay.textContent = 
            `${weekStart.toLocaleDateString('pt-BR', options)} - ${weekEnd.toLocaleDateString('pt-BR', options)}`;
    };

    const renderHeader = (weekStart) => {
        const header = document.createElement('div');
        header.className = 'agenda-header';
        
        // Espaço em branco para a coluna de horários
        header.appendChild(document.createElement('div')); 

        for (let i = 0; i < 7; i++) {
            const day = new Date(weekStart);
            day.setDate(weekStart.getDate() + i);
            const dayCell = document.createElement('div');
            dayCell.className = 'day-header';
            dayCell.innerHTML = `<span>${day.toLocaleDateString('pt-BR', { weekday: 'short' }).replace('.', '')}</span>
                                 <span class="day-number">${day.getDate()}</span>`;
            header.appendChild(dayCell);
        }
        agendaVisual.appendChild(header);
    };

    const renderTimeSlots = () => {
        const slotsContainer = document.createElement('div');
        slotsContainer.className = 'slots-container';

        // Coluna de Horários
        const timesColumn = document.createElement('div');
        timesColumn.className = 'times-column';
        for (let hour = 8; hour <= 18; hour++) {
            const timeCell = document.createElement('div');
            timeCell.className = 'time-label';
            timeCell.textContent = `${hour.toString().padStart(2, '0')}:00`;
            timesColumn.appendChild(timeCell);
        }
        slotsContainer.appendChild(timesColumn);

        // Colunas dos Dias da Semana
        const daysContainer = document.createElement('div');
        daysContainer.className = 'days-container';
        for (let day = 0; day < 7; day++) {
            const dayColumn = document.createElement('div');
            dayColumn.className = 'day-column';
            dayColumn.dataset.dayIndex = day;
            for (let hour = 8; hour <= 18; hour++) {
                const slot = document.createElement('div');
                slot.className = 'time-slot';
                slot.dataset.hour = hour;
                dayColumn.appendChild(slot);
            }
            daysContainer.appendChild(dayColumn);
        }
        slotsContainer.appendChild(daysContainer);
        agendaVisual.appendChild(slotsContainer);
    };

    const fetchAppointments = (weekStart) => {
        const startDateStr = `${weekStart.getFullYear()}-${(weekStart.getMonth() + 1).toString().padStart(2, '0')}-${weekStart.getDate().toString().padStart(2, '0')}`;
        
        fetch(`../api/consultas_semana.php?start_date=${startDateStr}`)
            .then(response => response.json())
            .then(data => {
                placeAppointments(data, weekStart);
            })
            .catch(error => console.error('Erro ao buscar consultas:', error));
    };

    const placeAppointments = (appointments, weekStart) => {
        appointments.forEach(consulta => {
            const consultaDate = new Date(consulta.data_consulta);
            const dayOfWeek = consultaDate.getDay(); // 0 (Dom) - 6 (Sáb)
            const hour = consultaDate.getHours();

            const dayColumn = agendaVisual.querySelector(`.day-column[data-day-index='${dayOfWeek}']`);
            if (dayColumn) {
                const slot = dayColumn.querySelector(`.time-slot[data-hour='${hour}']`);
                if (slot) {
                    const appointmentBlock = document.createElement('div');
                    appointmentBlock.className = 'appointment-block';
                    appointmentBlock.title = `${consulta.nome_paciente} - ${consulta.procedimento}`;
                    appointmentBlock.innerHTML = `<strong>${consulta.nome_paciente.split(' ')[0]}</strong><br>${consulta.procedimento}`;
                    
                    // Adicionar link para editar consulta
                    const link = document.createElement('a');
                    link.href = `consulta_form.php?action=edit&id=${consulta.id}`;
                    link.appendChild(appointmentBlock);

                    slot.appendChild(link);
                }
            }
        });
    };

    // Navegação
    prevWeekBtn.addEventListener('click', () => {
        currentDate.setDate(currentDate.getDate() - 7);
        renderCalendar(currentDate);
    });

    nextWeekBtn.addEventListener('click', () => {
        currentDate.setDate(currentDate.getDate() + 7);
        renderCalendar(currentDate);
    });

    // Renderização inicial
    renderCalendar(currentDate);
});