describe("TC-FS-05", () => {
    it("TC-FS-05", () => {
        cy.login();
        cy.viewport(1089, 825);
        cy.visit("/apps/files/files");

        cy.get(".files-fileList", { timeout: 10000 }).should("be.visible");
        cy.get(".action-share").first().click();

        cy.contains("button", "Create public link").click();

        cy.contains("label", "Set expiration date").click();

        const yesterday = new Date();
        yesterday.setDate(yesterday.getDate() - 1);
        const formattedDate = yesterday.toISOString().split('T')[0];

        cy.get('input[type="date"]').type(formattedDate);

        cy.contains("button", "Create").click();

        cy.contains("Expiration date cannot be in the past").should("be.visible");
    });
});